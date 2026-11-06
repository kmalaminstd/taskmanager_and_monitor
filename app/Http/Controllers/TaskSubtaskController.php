<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSubtask;
use App\Models\TaskHistory;
use Illuminate\Http\Request;

class TaskSubtaskController extends Controller
{
    public function store(Request $request, Task $task)
    {
        if (! auth()->user()->canMonitor()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'deadline' => ['nullable', 'date'],
        ]);

        $maxOrder = $task->subtasks()->max('sort_order') ?? 0;

        TaskSubtask::create([
            ...$data,
            'task_id' => $task->id,
            'sort_order' => $maxOrder + 1,
            'created_by' => auth()->id(),
            'status' => 'pending',
        ]);

        TaskHistory::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'action' => 'subtask_created',
            'description' => 'Subtask: ' . $data['title'],
        ]);

        return back()->with('success', 'Subtask added.');
    }

    public function update(Request $request, TaskSubtask $subtask)
    {
        $user = auth()->user();
        $canEdit = $user->canMonitor() || $subtask->assignee_id === $user->id;
        if (! $canEdit) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'deadline' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:pending,in_progress,completed,cancelled'],
        ]);

        if (isset($data['status']) && $data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        $subtask->update($data);

        return back()->with('success', 'Subtask updated.');
    }

    public function toggleComplete(TaskSubtask $subtask)
    {
        $user = auth()->user();
        if (! $user->canMonitor() && $subtask->assignee_id !== $user->id) {
            abort(403);
        }

        if ($subtask->status === 'completed') {
            $subtask->update(['status' => 'pending', 'completed_at' => null]);
        } else {
            $subtask->update(['status' => 'completed', 'completed_at' => now()]);
        }

        return back()->with('success', 'Subtask status updated.');
    }

    public function destroy(TaskSubtask $subtask)
    {
        if (! auth()->user()->canMonitor()) {
            abort(403);
        }
        $subtask->delete();
        return back()->with('success', 'Subtask deleted.');
    }
}
