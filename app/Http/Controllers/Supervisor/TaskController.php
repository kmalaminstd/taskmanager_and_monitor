<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaskRequest;
use App\Http\Requests\Admin\UpdateTaskRequest;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Services\TaskService;
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
        return view('supervisor.tasks.index', compact('assignments', 'assignees', 'categories', 'filters'));
    }

    public function create()
    {
        $assignees = User::where('role', 'assignee')->where('status', 'active')->orderBy('name')->get();
        $categories = TaskCategory::orderBy('name')->get();
        return view('supervisor.tasks.create', compact('assignees', 'categories'));
    }

    public function store(StoreTaskRequest $request)
    {
        // Allow supervisor to create - adjust authorize on request or use same validation
        $task = $this->taskService->createTask(
            $request->validated(),
            auth()->user(),
            $request->file('attachment')
        );
        return redirect()->route('supervisor.tasks.show', $task)
            ->with('success', 'Task created and assigned successfully.');
    }

    public function show(Task $task)
    {
        $task->load([
            'creator', 'category', 'assignments.assignee', 'assignments.assignedBy',
            'notes.user', 'attachments', 'histories.user',
            'transferHistories.previousAssignee', 'transferHistories.newAssignee',
        ]);
        return view('supervisor.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $task->load('activeAssignments');
        $assignees = User::where('role', 'assignee')->where('status', 'active')->orderBy('name')->get();
        $categories = TaskCategory::orderBy('name')->get();
        $currentAssigneeIds = $task->activeAssignments->pluck('assignee_id')->toArray();
        return view('supervisor.tasks.edit', compact('task', 'assignees', 'categories', 'currentAssigneeIds'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->updateTask($task, $request->validated(), auth()->user(), $request->file('attachment'));
        return redirect()->route('supervisor.tasks.show', $task)->with('success', 'Task updated.');
    }
}
