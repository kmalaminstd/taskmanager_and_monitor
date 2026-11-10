<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\TaskSubmission;
use App\Models\TaskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskAssignment::with(['task.category', 'assignedBy'])
            ->where('assignee_id', auth()->id())
            ->where('is_active', true);

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                      ->where('deadline', '<', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        $assignments = $query->orderBy('deadline')->paginate(12)->withQueryString();

        return view('assignee.tasks.index', compact('assignments'));
    }

    public function show(TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->load([
            'task.category',
            'task.attachments',
            'assignedBy',
            'notes.user',
            'submissions',
            'task.assistanceRequests' => function ($q) use ($assignment) {
                $q->where(function ($q2) use ($assignment) {
                    $q2->where('task_assignment_id', $assignment->id)
                       ->orWhereNull('task_assignment_id');
                })->with(['users', 'requester']);
            },
        ]);

        $assistances = $assignment->task
            ? $assignment->task->assistanceRequests
                ->filter(fn ($a) => !$a->task_assignment_id || (int)$a->task_assignment_id === (int)$assignment->id)
                ->values()
            : collect();

        return view('assignee.tasks.show', compact('assignment', 'assistances'));
    }

    public function start(TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        if ($assignment->status !== 'pending') {
            return back()->with('error', 'Only pending tasks can be started.');
        }

        DB::transaction(function () use ($assignment) {
            $assignment->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            TaskHistory::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'user_id' => auth()->id(),
                'action' => 'started',
                'description' => 'Task started by assignee',
            ]);
        });

        return back()->with('success', 'Task started successfully.');
    }

    public function submit(Request $request, TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        if (!in_array($assignment->status, ['in_progress', 'returned'])) {
            return back()->with('error', 'Only in-progress or returned tasks can be submitted.');
        }

        $data = $request->validate([
            'completion_details' => ['required', 'string'],
            'completion_note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($assignment, $data) {
            $assignment->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            TaskSubmission::create([
                'task_assignment_id' => $assignment->id,
                'submitted_by' => auth()->id(),
                'completion_details' => $data['completion_details'],
                'completion_note' => $data['completion_note'] ?? null,
                'status' => 'pending',
            ]);

            TaskHistory::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'user_id' => auth()->id(),
                'action' => 'submitted',
                'description' => 'Work submitted for approval',
            ]);
        });

        return redirect()
            ->route('assignee.tasks.show', $assignment)
            ->with('success', 'Task submitted successfully. Waiting for admin approval.');
    }

    protected function authorizeAssignment(TaskAssignment $assignment): void
    {
        if ($assignment->assignee_id !== auth()->id() || !$assignment->is_active) {
            abort(403, 'You are not authorized to access this assignment.');
        }
    }
}
