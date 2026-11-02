<?php

namespace App\Services;

use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceService
{
    /**
     * Get performance stats for a single user (or all if null).
     */
    public function getUserPerformance(?int $userId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = TaskAssignment::query()->where('is_active', true)
            ->orWhere(function ($q) {
                // Include completed even if later deactivated? Better: all non-cancelled
            });

        // Cleaner approach: exclude cancelled
        $query = TaskAssignment::where('status', '!=', 'cancelled');

        if ($userId) {
            $query->where('assignee_id', $userId);
        }

        if ($from) {
            $query->where('assigned_at', '>=', $from);
        }
        if ($to) {
            $query->where('assigned_at', '<=', $to);
        }

        $assignments = $query->get();

        $total = $assignments->count();
        $completed = $assignments->where('status', 'completed')->count();
        $pending = $assignments->where('status', 'pending')->count();
        $inProgress = $assignments->where('status', 'in_progress')->count();
        $submitted = $assignments->where('status', 'submitted')->count();
        $returned = $assignments->where('status', 'returned')->count();

        $overdue = $assignments->filter(function ($a) {
            return $a->deadline && $a->deadline->isPast()
                && !in_array($a->status, ['submitted', 'completed', 'cancelled']);
        })->count();

        $onTime = $assignments->filter(fn($a) => $a->status === 'completed' && $a->isOnTime())->count();
        $late = $assignments->filter(fn($a) => $a->status === 'completed' && $a->isLateCompleted())->count();

        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        $onTimeRate = $completed > 0 ? round(($onTime / $completed) * 100, 1) : 0;

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'submitted' => $submitted,
            'returned' => $returned,
            'overdue' => $overdue,
            'on_time' => $onTime,
            'late' => $late,
            'completion_rate' => $completionRate,
            'on_time_rate' => $onTimeRate,
        ];
    }

    /**
     * Performance for all employees (admin view).
     */
    public function getAllEmployeesPerformance(?Carbon $from = null, ?Carbon $to = null): array
    {
        $employees = User::where('role', 'assignee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $result = [];
        foreach ($employees as $employee) {
            $stats = $this->getUserPerformance($employee->id, $from, $to);
            $result[] = [
                'user' => $employee,
                'stats' => $stats,
            ];
        }

        return $result;
    }

    /**
     * Data for charts.
     */
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

        // Last 6 months completion trend
        $trend = [];
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
