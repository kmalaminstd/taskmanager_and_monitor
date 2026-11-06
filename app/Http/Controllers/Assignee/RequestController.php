<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Models\TaskAssignment;
use App\Models\TaskExtensionRequest;
use App\Models\TaskTransferRequest;
use App\Models\TaskAssistanceRequest;
use App\Models\TaskHistory;
use App\Models\User;
use App\Notifications\TaskStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function requestExtension(Request $request, TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $data = $request->validate([
            'requested_deadline' => ['required', 'date', 'after:today'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

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

        if ((int) $data['requested_new_assignee_id'] === (int) auth()->id()) {
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

    public function requestAssistance(Request $request, TaskAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $data = $request->validate([
            'helper_ids' => ['required', 'array', 'min:1'],
            'helper_ids.*' => ['exists:users,id'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $helperIds = array_values(array_unique(array_map('intval', $data['helper_ids'])));
        $helperIds = array_values(array_filter($helperIds, fn ($id) => $id !== (int) auth()->id()));

        if (empty($helperIds)) {
            return back()->with('error', 'Please select at least one other team member.');
        }

        $validHelpers = User::whereIn('id', $helperIds)
            ->whereIn('role', ['assignee', 'supervisor'])
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        if (empty($validHelpers)) {
            return back()->with('error', 'Selected helpers are not valid.');
        }

        DB::transaction(function () use ($assignment, $validHelpers, $data) {
            $assistance = TaskAssistanceRequest::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'requester_id' => auth()->id(),
                'description' => $data['description'],
                'status' => 'pending',
            ]);

            foreach ($validHelpers as $helperId) {
                $assistance->users()->attach($helperId, ['status' => 'pending']);
            }

            TaskHistory::create([
                'task_id' => $assignment->task_id,
                'task_assignment_id' => $assignment->id,
                'user_id' => auth()->id(),
                'action' => 'assistance_requested',
                'description' => 'Assistance requested from ' . count($validHelpers) . ' member(s)',
            ]);

            $taskTitle = $assignment->task->title ?? 'a task';
            foreach (User::whereIn('id', $validHelpers)->get() as $helper) {
                $helper->notify(new TaskStatusNotification(
                    'Assistance Requested',
                    auth()->user()->name . ' asked for your help on: ' . $taskTitle,
                    route('assignee.assistance.show', $assistance->id),
                    'assistance'
                ));
            }

            foreach (User::whereIn('role', ['admin', 'supervisor'])->where('status', 'active')->get() as $monitor) {
                $url = $monitor->role === 'admin'
                    ? route('admin.assistance.index')
                    : route('supervisor.assistance.index');
                $monitor->notify(new TaskStatusNotification(
                    'New Assistance Request',
                    auth()->user()->name . ' requested help on: ' . $taskTitle,
                    $url,
                    'assistance'
                ));
            }
        });

        return back()->with('success', 'Assistance request sent to selected team members.');
    }

    protected function authorizeAssignment(TaskAssignment $assignment): void
    {
        if ($assignment->assignee_id !== auth()->id() || ! $assignment->is_active) {
            abort(403);
        }
    }
}
