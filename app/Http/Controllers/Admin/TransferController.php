<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function __construct(protected TransferService $transferService) {}

    public function create(Task $task)
    {
        $task->load('activeAssignments.assignee');
        $assignees = User::where('role', 'assignee')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.transfers.create', compact('task', 'assignees'));
    }

    public function store(Request $request, Task $task)
    {
        $data = $request->validate([
            'new_assignee_ids' => ['required', 'array', 'min:1'],
            'new_assignee_ids.*' => ['exists:users,id'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'keep_existing' => ['nullable', 'boolean'],
        ]);

        $this->transferService->adminDirectTransfer(
            $task,
            $data['new_assignee_ids'],
            $data['reason'],
            $data['notes'] ?? null,
            auth()->user(),
            $request->boolean('keep_existing')
        );

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'Task transfer completed successfully.');
    }
}
