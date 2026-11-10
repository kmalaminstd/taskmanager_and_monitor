<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\PersonalTask;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(ReminderService $reminders)
    {
        $userId = auth()->id();

        $stats = [
            'total_assigned' => TaskAssignment::where('assignee_id', $userId)->where('is_active', true)->count(),
            'pending' => TaskAssignment::where('assignee_id', $userId)->where('status', 'pending')->where('is_active', true)->count(),
            'in_progress' => TaskAssignment::where('assignee_id', $userId)->where('status', 'in_progress')->where('is_active', true)->count(),
            'submitted' => TaskAssignment::where('assignee_id', $userId)->where('status', 'submitted')->where('is_active', true)->count(),
            'completed' => TaskAssignment::where('assignee_id', $userId)->where('status', 'completed')->count(),
            'overdue' => TaskAssignment::where('assignee_id', $userId)
                ->where('is_active', true)
                ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                ->where('deadline', '<', now())
                ->count(),
            'due_today' => TaskAssignment::where('assignee_id', $userId)
                ->where('is_active', true)
                ->whereDate('deadline', today())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'personal_tasks' => PersonalTask::where('user_id', $userId)->whereNotIn('status', ['completed', 'cancelled'])->count(),
        ];

        $recentAssignments = TaskAssignment::with(['task', 'assignedBy'])
            ->where('assignee_id', $userId)
            ->where('is_active', true)
            ->latest('assigned_at')
            ->take(8)
            ->get();

        $upcomingDeadlines = TaskAssignment::with('task')
            ->where('assignee_id', $userId)
            ->where('is_active', true)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->take(5)
            ->get();

        $reminderData = $reminders->forAssignee($userId);

        // Completion streak stats for recurring
        $recurringDone = collect($reminderData['assigned'])->where('done_this_period', true)->count()
            + collect($reminderData['personal'])->where('done_this_period', true)->count();
        $recurringTotal = collect($reminderData['assigned'])->count()
            + collect($reminderData['personal'])->count();

        return view('assignee.dashboard', compact(
            'stats', 'recentAssignments', 'upcomingDeadlines', 'reminderData', 'recurringDone', 'recurringTotal'
        ));
    }
}
