<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Models\TaskSubmission;
use App\Models\TaskExtensionRequest;
use App\Models\TaskTransferRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tasks' => Task::count(),
            'total_assigned' => TaskAssignment::where('is_active', true)->count(),
            'completed' => TaskAssignment::where('status', 'completed')->count(),
            'pending' => TaskAssignment::where('status', 'pending')->where('is_active', true)->count(),
            'in_progress' => TaskAssignment::where('status', 'in_progress')->where('is_active', true)->count(),
            'submitted' => TaskAssignment::where('status', 'submitted')->where('is_active', true)->count(),
            'overdue' => TaskAssignment::where('is_active', true)
                ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                ->where('deadline', '<', now())
                ->count(),
            'due_today' => TaskAssignment::where('is_active', true)
                ->whereDate('deadline', today())
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count(),
            'team_members' => User::where('role', 'assignee')->where('status', 'active')->count(),
            'pending_approvals' => TaskSubmission::where('status', 'pending')->count()
                + TaskExtensionRequest::where('status', 'pending')->count()
                + TaskTransferRequest::where('status', 'pending')->count(),
        ];

        $recentTasks = Task::with(['creator', 'activeAssignments.assignee'])
            ->latest()
            ->take(8)
            ->get();

        $overdueAssignments = TaskAssignment::with(['task', 'assignee'])
            ->where('is_active', true)
            ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
            ->where('deadline', '<', now())
            ->orderBy('deadline')
            ->take(8)
            ->get();

        return view('supervisor.dashboard', compact('stats', 'recentTasks', 'overdueAssignments'));
    }
}
