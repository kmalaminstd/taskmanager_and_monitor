<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Models\TaskCategory;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskAssignment::with(['task.category', 'assignee', 'assignedBy'])
            ->where('is_active', true);

        // Filters
        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                      ->where('deadline', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        if ($request->filled('priority')) {
            $query->where(function ($q) use ($request) {
                $q->where('priority', $request->priority)
                  ->orWhereHas('task', fn($t) => $t->where('priority', $request->priority));
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('assigned_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('assigned_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'deadline');
        $direction = $request->get('direction', 'asc');
        if (in_array($sort, ['deadline', 'assigned_at', 'status', 'priority'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('deadline', 'asc');
        }

        $assignments = $query->paginate(20)->withQueryString();

        $assignees = User::where('role', 'assignee')->where('status', 'active')->orderBy('name')->get();
        $categories = TaskCategory::orderBy('name')->get();

        // Summary counts
        $counts = [
            'total' => TaskAssignment::where('is_active', true)->count(),
            'pending' => TaskAssignment::where('is_active', true)->where('status', 'pending')->count(),
            'in_progress' => TaskAssignment::where('is_active', true)->where('status', 'in_progress')->count(),
            'submitted' => TaskAssignment::where('is_active', true)->where('status', 'submitted')->count(),
            'overdue' => TaskAssignment::where('is_active', true)
                ->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                ->where('deadline', '<', now())->count(),
        ];

        return view('admin.assignments.index', compact('assignments', 'assignees', 'categories', 'counts'));
    }

    public function show(TaskAssignment $assignment)
    {
        $assignment->load([
            'task.category', 'task.creator', 'task.attachments',
            'assignee', 'assignedBy',
            'notes.user', 'submissions.submitter',
            'extensionRequests', 'transferRequests',
            'histories.user'
        ]);

        return view('admin.assignments.show', compact('assignment'));
    }
}
