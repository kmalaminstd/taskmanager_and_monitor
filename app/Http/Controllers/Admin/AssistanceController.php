<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskAssistanceRequest;
use App\Models\TaskHistory;
use App\Models\User;
use App\Notifications\TaskStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssistanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TaskAssistanceRequest::with(['task', 'requester', 'users', 'assignment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('task', fn ($q) => $q->where('title', 'like', '%' . $request->search . '%'));
        }

        $requests = $query->latest()->paginate(20)->withQueryString();
        $counts = [
            'total' => TaskAssistanceRequest::count(),
            'pending' => TaskAssistanceRequest::where('status', 'pending')->count(),
            'accepted' => TaskAssistanceRequest::where('status', 'accepted')->count(),
            'completed' => TaskAssistanceRequest::where('status', 'completed')->count(),
        ];

        return view('admin.assistance.index', compact('requests', 'counts'));
    }

    public function show(TaskAssistanceRequest $assistance)
    {
        $assistance->load(['task', 'requester', 'users', 'assignment.assignee']);
        return view('admin.assistance.show', compact('assistance'));
    }

    /**
     * Admin assigns helpers to assist a specific assignee on a task.
     */
    public function storeForAssignment(Request $request, Task $task, TaskAssignment $assignment)
    {
        if ($assignment->task_id !== $task->id) {
            abort(404);
        }

        if (! $assignment->is_active) {
            return back()->with('error', 'Cannot add assistance on an inactive assignment.');
        }

        $data = $request->validate([
            'helper_ids' => ['required', 'array', 'min:1'],
            'helper_ids.*' => ['exists:users,id'],
            'description' => ['required', 'string', 'max:2000'],
        ]);

        $helperIds = array_values(array_unique(array_map('intval', $data['helper_ids'])));
        // Cannot assist yourself as the assignee
        $helperIds = array_values(array_filter($helperIds, fn ($id) => $id !== (int) $assignment->assignee_id));

        if (empty($helperIds)) {
            return back()->with('error', 'Select at least one helper other than the assignee.');
        }

        $validHelpers = User::whereIn('id', $helperIds)
            ->whereIn('role', ['assignee', 'supervisor'])
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        if (empty($validHelpers)) {
            return back()->with('error', 'Selected helpers are not valid.');
        }

        DB::transaction(function () use ($task, $assignment, $validHelpers, $data) {
            $assistance = TaskAssistanceRequest::create([
                'task_id' => $task->id,
                'task_assignment_id' => $assignment->id,
                'requester_id' => $assignment->assignee_id, // help is for this assignee
                'description' => '[Assigned by Admin: ' . auth()->user()->name . '] ' . $data['description'],
                'status' => 'accepted', // admin-initiated
            ]);

            foreach ($validHelpers as $helperId) {
                $assistance->users()->attach($helperId, [
                    'status' => 'accepted', // admin assigned — ready to work
                    'responded_at' => now(),
                ]);
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'task_assignment_id' => $assignment->id,
                'user_id' => auth()->id(),
                'action' => 'assistance_assigned_by_admin',
                'description' => 'Admin assigned ' . count($validHelpers) . ' helper(s) for '
                    . ($assignment->assignee->name ?? 'assignee'),
            ]);

            $taskTitle = $task->title;
            $assigneeName = $assignment->assignee->name ?? 'Assignee';

            // Notify helpers
            foreach (User::whereIn('id', $validHelpers)->get() as $helper) {
                $helper->notify(new TaskStatusNotification(
                    'Assistance Assigned',
                    'Admin assigned you to help ' . $assigneeName . ' on: ' . $taskTitle,
                    route('assignee.assistance.show', $assistance->id),
                    'assistance'
                ));
            }

            // Notify the assignee
            if ($assignment->assignee) {
                $assignment->assignee->notify(new TaskStatusNotification(
                    'Helpers Assigned',
                    'Admin assigned helper(s) for your task: ' . $taskTitle,
                    route('assignee.assistance.show', $assistance->id),
                    'assistance'
                ));
            }
        });

        return back()->with('success', 'Assistance assigned successfully for this assignee.');
    }

    /**
     * Admin removes an entire assistance request.
     */
    public function destroy(TaskAssistanceRequest $assistance)
    {
        $taskId = $assistance->task_id;
        $assignmentId = $assistance->task_assignment_id;

        TaskHistory::create([
            'task_id' => $taskId,
            'task_assignment_id' => $assignmentId,
            'user_id' => auth()->id(),
            'action' => 'assistance_removed_by_admin',
            'description' => 'Admin removed assistance request #' . $assistance->id,
        ]);

        $assistance->users()->detach();
        $assistance->delete();

        return back()->with('success', 'Assistance removed successfully.');
    }

}
