<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskCategory;
use App\Models\User;
use App\Services\TaskService;
use App\Services\WorkloadService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function index(Request $request)
    {
        $filters = $request->only([
            'status', 'assignee_id', 'priority', 'search',
            'date_from', 'date_to', 'sort', 'direction', 'per_page'
        ]);

        $assignments = $this->taskService->getFilteredAssignments($filters);

        $assignees = User::where('role', 'assignee')->where('status', 'active')->orderBy('name')->get();
        $categories = TaskCategory::orderBy('name')->get();

        return view('admin.tasks.index', compact('assignments', 'assignees', 'categories', 'filters'));
    }

    public function create(WorkloadService $workloadService)
    {
        $assignees = User::where('role', 'assignee')->where('status', 'active')->orderBy('name')->get();
        $categories = TaskCategory::orderBy('name')->get();

        $workloads = $workloadService->forAllAssignees()->keyBy(fn ($w) => $w['user']->id);
        return view('admin.tasks.create', compact('assignees', 'categories', 'workloads'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask(
            $request->validated(),
            auth()->user(),
            $request->file('attachment')
        );

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'Task created and assigned successfully.');
    }

    public function show(Task $task)
    {
        $task->load([
            'creator',
            'category',
            'assignments.assignee',
            'assistanceRequests.users',
            'assistanceRequests.requester',
            'assistanceRequests.assignment.assignee',
            'assignments.assignedBy',
            'notes.user',
            'attachments.uploader',
            'histories.user',
            'transferHistories.previousAssignee',
            'transferHistories.newAssignee',
            'transferHistories.transferredBy',
        ]);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $task->load('activeAssignments');
        $assignees = User::where('role', 'assignee')->where('status', 'active')->orderBy('name')->get();
        $categories = TaskCategory::orderBy('name')->get();
        $currentAssigneeIds = $task->activeAssignments->pluck('assignee_id')->toArray();

        return view('admin.tasks.edit', compact('task', 'assignees', 'categories', 'currentAssigneeIds'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->updateTask(
            $task,
            $request->validated(),
            auth()->user(),
            $request->file('attachment')
        );

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete(); // soft delete

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
