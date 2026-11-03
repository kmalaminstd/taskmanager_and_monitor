<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TaskAssignment;
use App\Models\PersonalTask;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'assignee');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }
        $members = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('supervisor.team.index', compact('members'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'assignee') {
            abort(404);
        }

        $assignedQuery = TaskAssignment::where('assignee_id', $user->id);
        $assignedStats = [
            'total' => (clone $assignedQuery)->count(),
            'pending' => (clone $assignedQuery)->where('status', 'pending')->where('is_active', true)->count(),
            'in_progress' => (clone $assignedQuery)->where('status', 'in_progress')->where('is_active', true)->count(),
            'submitted' => (clone $assignedQuery)->where('status', 'submitted')->where('is_active', true)->count(),
            'completed' => (clone $assignedQuery)->where('status', 'completed')->count(),
            'overdue' => (clone $assignedQuery)->where('is_active', true)
                ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                ->where('deadline', '<', now())->count(),
        ];

        $personalQuery = PersonalTask::where('user_id', $user->id);
        $personalStats = [
            'total' => (clone $personalQuery)->count(),
            'pending' => (clone $personalQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $personalQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $personalQuery)->where('status', 'completed')->count(),
        ];

        $assignedTasks = TaskAssignment::with(['task.category', 'assignedBy'])
            ->where('assignee_id', $user->id)
            ->orderByDesc('assigned_at')
            ->paginate(10, ['*'], 'assigned_page');

        $personalTasks = PersonalTask::where('user_id', $user->id)
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('deadline')
            ->paginate(10, ['*'], 'personal_page');

        return view('supervisor.team.show', compact(
            'user', 'assignedStats', 'personalStats', 'assignedTasks', 'personalTasks'
        ));
    }
}
