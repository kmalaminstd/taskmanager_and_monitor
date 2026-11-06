<?php

namespace App\Services;

use App\Models\TaskAssignment;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceService
{
    /**
     * Resolve preset date ranges.
     * @return array{0:?Carbon,1:?Carbon}
     */
    public function resolvePeriod(?string $period, ?string $from = null, ?string $to = null): array
    {
        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
            'custom' => [
                $from ? Carbon::parse($from)->startOfDay() : null,
                $to ? Carbon::parse($to)->endOfDay() : null,
            ],
            default => [null, null], // all time
        };
    }

    /**
     * Performance for one user (or system-wide if null).
     * Date filters apply to relevant activity timestamps, not only assigned_at.
     */
    public function getUserPerformance(?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        // Base: all non-cancelled assignments for the user
        $base = TaskAssignment::query()->where('status', '!=', 'cancelled');
        if ($userId) {
            $base->where('assignee_id', $userId);
        }

        // Assigned in period (or all)
        $assignedQ = (clone $base);
        if ($from) {
            $assignedQ->where('assigned_at', '>=', $from);
        }
        if ($to) {
            $assignedQ->where('assigned_at', '<=', $to);
        }
        $assigned = $assignedQ->count();

        // Active snapshot (current state, not period-bound for pending/in_progress/overdue)
        $activeBase = TaskAssignment::query()
            ->where('is_active', true)
            ->whereNotIn('status', ['cancelled']);
        if ($userId) {
            $activeBase->where('assignee_id', $userId);
        }

        $pending = (clone $activeBase)->where('status', 'pending')->count();
        $inProgress = (clone $activeBase)->where('status', 'in_progress')->count();
        $submitted = (clone $activeBase)->where('status', 'submitted')->count();

        $overdue = (clone $activeBase)
            ->whereNotIn('status', ['submitted', 'completed'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->count();

        // Completed in period (use completed_at)
        $completedQ = TaskAssignment::query()
            ->where('status', 'completed');
        if ($userId) {
            $completedQ->where('assignee_id', $userId);
        }
        if ($from) {
            $completedQ->where('completed_at', '>=', $from);
        }
        if ($to) {
            $completedQ->where('completed_at', '<=', $to);
        }
        $completedAssignments = $completedQ->get();

        $completed = $completedAssignments->count();
        $onTime = $completedAssignments->filter(fn ($a) => $a->isOnTime())->count();
        $late = $completedAssignments->filter(fn ($a) => $a->isLateCompleted())->count();

        // Average completion time (hours) start → submit
        $avgHours = null;
        $timed = $completedAssignments->filter(fn ($a) => $a->started_at && $a->submitted_at);
        if ($timed->count() > 0) {
            $avgHours = round(
                $timed->avg(fn ($a) => $a->started_at->diffInMinutes($a->submitted_at) / 60),
                1
            );
        }

        // Returned submissions in period
        $returnedQ = TaskSubmission::query()->where('status', 'returned');
        if ($userId) {
            $returnedQ->whereHas('assignment', fn ($q) => $q->where('assignee_id', $userId));
        }
        if ($from) {
            $returnedQ->where('created_at', '>=', $from);
        }
        if ($to) {
            $returnedQ->where('created_at', '<=', $to);
        }
        $returned = $returnedQ->count();

        // Workload (current)
        $activeCount = (clone $activeBase)->whereIn('status', ['pending', 'in_progress', 'submitted', 'returned'])->count();
        $workload = app(WorkloadService::class)->levelFromCount($activeCount, $overdue);

        $completionRate = $assigned > 0 ? round(($completed / max($assigned, 1)) * 100, 1) : 0;
        $onTimeRate = $completed > 0 ? round(($onTime / $completed) * 100, 1) : 0;

        // Quality score (weighted): on-time 50%, low return rate 30%, completion 20%
        $returnRate = $completed > 0 ? min($returned / max($completed, 1), 1) : 0;
        $qualityScore = round(
            ($onTimeRate * 0.5) + ((1 - $returnRate) * 100 * 0.3) + (min($completionRate, 100) * 0.2),
            1
        );

        return [
            'assigned' => $assigned,
            'total' => $assigned, // backward compat
            'completed' => $completed,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'submitted' => $submitted,
            'returned' => $returned,
            'overdue' => $overdue,
            'on_time' => $onTime,
            'late' => $late,
            'avg_completion_hours' => $avgHours,
            'active_workload' => $activeCount,
            'workload_level' => $workload['level'],
            'workload_label' => $workload['label'],
            'completion_rate' => $completionRate,
            'on_time_rate' => $onTimeRate,
            'quality_score' => $qualityScore,
        ];
    }

    public function getAllEmployeesPerformance(?Carbon $from = null, ?Carbon $to = null): array
    {
        $employees = User::where('role', 'assignee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $result = [];
        foreach ($employees as $employee) {
            $result[] = [
                'user' => $employee,
                'stats' => $this->getUserPerformance($employee->id, $from, $to),
            ];
        }

        return $result;
    }

    public function getChartData(?int $userId = null): array
    {
        $base = TaskAssignment::where('status', '!=', 'cancelled');
        if ($userId) {
            $base->where('assignee_id', $userId);
        }

        $statusDistribution = (clone $base)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $trend = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = (clone $base)
                ->where('status', 'completed')
                ->whereYear('completed_at', $month->year)
                ->whereMonth('completed_at', $month->month)
                ->count();
            $trend['labels'][] = $month->format('M Y');
            $trend['data'][] = $count;
        }

        return [
            'status' => $statusDistribution,
            'trend' => $trend,
        ];
    }
}
