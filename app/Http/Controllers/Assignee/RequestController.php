<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\TaskExtensionRequest;
use App\Models\TaskTransferRequest;
use App\Models\User;
use App\Models\TaskHistory;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function requestExtension(Request $request, TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $data = $request->validate([
            'requested_deadline' => ['required', 'date', 'after:today'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        // Prevent duplicate pending requests
        $exists = TaskExtensionRequest::where('task_assignment_id', $assignment->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending extension request for this task.');
        }

        TaskExtensionRequest::create([
            'task_assignment_id' => $assignment->id,
            'requested_by' => auth()->id(),
            'current_deadline' => $assignment->deadline ?? $assignment->task->deadline,
            'requested_deadline' => $data['requested_deadline'],
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        TaskHistory::create([
            'task_id' => $assignment->task_id,
            'task_assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
            'action' => 'extension_requested',
            'description' => 'Deadline extension requested',
        ]);

        return back()->with('success', 'Extension request submitted. Waiting for admin approval.');
    }

    public function requestTransfer(Request $request, TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $data = $request->validate([
            'requested_new_assignee_id' => ['required', 'exists:users,id'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        if ($data['requested_new_assignee_id'] == auth()->id()) {
            return back()->with('error', 'You cannot transfer a task to yourself.');
        }

        $exists = TaskTransferRequest::where('task_assignment_id', $assignment->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending transfer request for this task.');
        }

        TaskTransferRequest::create([
            'task_assignment_id' => $assignment->id,
            'requested_by' => auth()->id(),
            'requested_new_assignee_id' => $data['requested_new_assignee_id'],
            'reason' => $data['reason'],
            'status' => 'pending',
        ]);

        TaskHistory::create([
            'task_id' => $assignment->task_id,
            'task_assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
            'action' => 'transfer_requested',
            'description' => 'Transfer request submitted',
        ]);

        return back()->with('success', 'Transfer request submitted. Waiting for admin approval.');
    }

    protected function authorizeAssignment(TaskAssignment $assignment): void
    {
        if ($assignment->assignee_id !== auth()->id() || !$assignment->is_active) {
            abort(403);
        }
    }
}
