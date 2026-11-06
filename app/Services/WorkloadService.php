<?php

namespace App\Services;

use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Support\Collection;

class WorkloadService
{
    /** Thresholds for active tasks */
    public int $lowMax = 3;
    public int $normalMax = 7;
    // above normalMax => high

    public function levelFromCount(int $active, int $overdue = 0): array
    {
        // Overdue pushes severity up
        if ($overdue >= 3 || $active > $this->normalMax) {
            $level = 'high';
            $label = 'High';
            $color = 'danger';
        } elseif ($active <= $this->lowMax && $overdue === 0) {
            $level = 'low';
            $label = 'Low';
            $color = 'success';
        } else {
            $level = 'normal';
            $label = 'Normal';
            $color = 'warning';
        }

        return compact('level', 'label', 'color', 'active', 'overdue');
    }

    public function forUser(User $user): array
    {
        $active = TaskAssignment::where('assignee_id', $user->id)
            ->where('is_active', true)
            ->whereIn('status', ['pending', 'in_progress', 'submitted', 'returned'])
            ->count();

        $overdue = TaskAssignment::where('assignee_id', $user->id)
            ->where('is_active', true)
            ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->count();

        $data = $this->levelFromCount($active, $overdue);
        $data['user'] = $user;
        $data['active_tasks'] = $active;
        $data['overdue_tasks'] = $overdue;

        return $data;
    }

    /**
     * @return Collection<int, array>
     */
    public function forAllAssignees(): Collection
    {
        return User::where('role', 'assignee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => $this->forUser($u))
            ->sortByDesc('active_tasks')
            ->values();
    }

    /** For assignee select options with workload text */
    public function assigneeOptions(): Collection
    {
        return $this->forAllAssignees();
    }
}
