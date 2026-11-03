<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Http\Request;

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
            'pending_approvals' => TaskAssignment::where('status', 'submitted')->count(),
            'total_users' => User::where('role', 'assignee')->where('status', 'active')->count(),
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

        return view('admin.dashboard', compact('stats', 'recentTasks', 'overdueAssignments'));
    }
}
