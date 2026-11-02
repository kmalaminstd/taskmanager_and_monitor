<?php

namespace App\Http\Controllers\Assignee;

use App\Http\Controllers\Controller;
use App\Models\PersonalTask;
use Illuminate\Http\Request;

class PersonalTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = PersonalTask::where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                       ->orderBy('deadline')
                       ->paginate(15)
                       ->withQueryString();

        return view('assignee.personal-tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('assignee.personal-tasks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:high,medium,low'],
            'frequency' => ['required', 'in:one_time,daily,weekly,monthly'],
        ]);

        PersonalTask::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('assignee.personal-tasks.index')
            ->with('success', 'Personal task created.');
    }

    public function edit(PersonalTask $personalTask)
    {
        $this->authorizeOwner($personalTask);
        return view('assignee.personal-tasks.edit', compact('personalTask'));
    }

    public function update(Request $request, PersonalTask $personalTask)
    {
        $this->authorizeOwner($personalTask);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:high,medium,low'],
            'frequency' => ['required', 'in:one_time,daily,weekly,monthly'],
            'status' => ['required', 'in:pending,in_progress,completed,cancelled'],
        ]);

        if ($data['status'] === 'completed' && $personalTask->status !== 'completed') {
            $data['completed_at'] = now();
        }

        $personalTask->update($data);

        return redirect()
            ->route('assignee.personal-tasks.index')
            ->with('success', 'Personal task updated.');
    }

    public function destroy(PersonalTask $personalTask)
    {
        $this->authorizeOwner($personalTask);
        $personalTask->delete();

        return back()->with('success', 'Personal task deleted.');
    }

    public function markComplete(PersonalTask $personalTask)
    {
        $this->authorizeOwner($personalTask);

        $personalTask->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Task marked as completed.');
    }

    protected function authorizeOwner(PersonalTask $task): void
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
