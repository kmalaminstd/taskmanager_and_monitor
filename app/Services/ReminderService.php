<?php

namespace App\Services;

use App\Models\PersonalTask;
use App\Models\TaskAssignment;
use App\Models\RecurringCompletion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReminderService
{
    /**
     * Build assignee dashboard reminders.
     */
    public function forAssignee(int $userId): array
    {
        $today = now()->startOfDay();
        $weekday = (int) $today->dayOfWeek; // 0 Sun - 6 Sat
        $monthday = (int) $today->day;

        $assignedReminders = $this->assignedReminders($userId, $today, $weekday, $monthday);
        $personalReminders = $this->personalReminders($userId, $today, $weekday, $monthday);

        return [
            'assigned' => $assignedReminders,
            'personal' => $personalReminders,
            'today' => $today,
        ];
    }

    protected function assignedReminders(int $userId, Carbon $today, int $weekday, int $monthday): Collection
    {
        $assignments = TaskAssignment::with(['task'])
            ->where('assignee_id', $userId)
            ->where('is_active', true)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();

        $items = collect();

        foreach ($assignments as $a) {
            $task = $a->task;
            if (! $task) {
                continue;
            }

            $deadline = $a->deadline ?? $task->deadline;
            $freq = $task->task_type === 'recurring' ? ($task->frequency ?? 'one_time') : 'one_time';
            $daysBefore = $task->reminder_days_before ?? 1;

            $reason = $this->matchReason($freq, $deadline, $today, $weekday, $monthday, $daysBefore, $task->recurrence_weekday, $task->recurrence_monthday);
            if (! $reason) {
                continue;
            }

            $periodKey = in_array($freq, ['daily', 'weekly', 'monthly'], true)
                ? RecurringCompletion::periodKey($freq, $today)
                : null;

            $doneThisPeriod = false;
            if ($periodKey) {
                $doneThisPeriod = RecurringCompletion::where('user_id', $userId)
                    ->where('taskable_type', TaskAssignment::class)
                    ->where('taskable_id', $a->id)
                    ->where('period_key', $periodKey)
                    ->exists();
            }

            $items->push([
                'type' => 'assigned',
                'source' => $a,
                'title' => $task->title,
                'frequency' => $freq,
                'deadline' => $deadline,
                'status' => $a->status,
                'reason' => $reason,
                'priority' => $a->priority ?? $task->priority,
                'done_this_period' => $doneThisPeriod,
                'period_key' => $periodKey,
                'url' => route('assignee.tasks.show', $a->id),
            ]);
        }

        return $items->sortBy(fn ($i) => $i['done_this_period'] ? 1 : 0)->values();
    }

    protected function personalReminders(int $userId, Carbon $today, int $weekday, int $monthday): Collection
    {
        $tasks = PersonalTask::where('user_id', $userId)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $items = collect();

        foreach ($tasks as $t) {
            $freq = $t->frequency ?? 'one_time';
            $daysBefore = $t->reminder_days_before ?? 1;

            // One-time completed tasks: skip
            if ($freq === 'one_time' && $t->status === 'completed') {
                continue;
            }

            $reason = $this->matchReason($freq, $t->deadline, $today, $weekday, $monthday, $daysBefore, $t->recurrence_weekday, $t->recurrence_monthday);
            if (! $reason) {
                continue;
            }

            $periodKey = in_array($freq, ['daily', 'weekly', 'monthly'], true)
                ? RecurringCompletion::periodKey($freq, $today)
                : null;

            $doneThisPeriod = false;
            if ($periodKey) {
                $doneThisPeriod = RecurringCompletion::where('user_id', $userId)
                    ->where('taskable_type', PersonalTask::class)
                    ->where('taskable_id', $t->id)
                    ->where('period_key', $periodKey)
                    ->exists();
            } elseif ($freq === 'one_time') {
                $doneThisPeriod = $t->status === 'completed';
            }

            $items->push([
                'type' => 'personal',
                'source' => $t,
                'title' => $t->title,
                'frequency' => $freq,
                'deadline' => $t->deadline,
                'status' => $t->status,
                'reason' => $reason,
                'priority' => $t->priority,
                'done_this_period' => $doneThisPeriod,
                'period_key' => $periodKey,
                'url' => route('assignee.personal-tasks.edit', $t->id),
            ]);
        }

        return $items->sortBy(fn ($i) => $i['done_this_period'] ? 1 : 0)->values();
    }

    /**
     * Why this task appears in today's reminders.
     */
    protected function matchReason(
        string $freq,
        $deadline,
        Carbon $today,
        int $weekday,
        int $monthday,
        int $daysBefore,
        $recurrenceWeekday,
        $recurrenceMonthday
    ): ?string {
        $deadline = $deadline ? Carbon::parse($deadline)->startOfDay() : null;

        // Deadline approaching (default 1 day before)
        if ($deadline) {
            $reminderDate = $deadline->copy()->subDays(max(0, $daysBefore));
            if ($today->equalTo($reminderDate)) {
                return 'deadline_soon'; // due tomorrow (or N days)
            }
            if ($today->equalTo($deadline)) {
                return 'deadline_today';
            }
            if ($deadline->lt($today) && $freq === 'one_time') {
                return 'overdue';
            }
        }

        if ($freq === 'daily') {
            return 'daily';
        }

        if ($freq === 'weekly') {
            if ($recurrenceWeekday !== null && (int) $recurrenceWeekday === $weekday) {
                return 'weekly_day';
            }
            // If no weekday set, still show weekly items every day? Better only on configured day or Mon if unset
            if ($recurrenceWeekday === null && $weekday === 1) {
                return 'weekly_default';
            }
        }

        if ($freq === 'monthly') {
            if ($recurrenceMonthday !== null && (int) $recurrenceMonthday === $monthday) {
                return 'monthly_day';
            }
            if ($recurrenceMonthday === null && $monthday === 1) {
                return 'monthly_default';
            }
        }

        return null;
    }

    public function markComplete(int $userId, string $type, int $id, string $frequency): void
    {
        $taskableType = $type === 'personal' ? PersonalTask::class : TaskAssignment::class;
        $periodKey = RecurringCompletion::periodKey($frequency);

        RecurringCompletion::updateOrCreate(
            [
                'user_id' => $userId,
                'taskable_type' => $taskableType,
                'taskable_id' => $id,
                'period_key' => $periodKey,
            ],
            [
                'frequency' => $frequency,
                'completed_at' => now(),
            ]
        );

        // For one_time personal, also mark task completed
        if ($type === 'personal' && $frequency === 'one_time') {
            PersonalTask::where('id', $id)->where('user_id', $userId)->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}
