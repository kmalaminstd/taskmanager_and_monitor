<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskNote;
use App\Models\TaskNoteAttachment;
use App\Models\TaskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorizeAccess($task);

        $rules = [
            'note' => ['required', 'string', 'max:5000'],
            'attachments.*' => ['nullable', 'file', 'max:5120'],
        ];
        if (Schema::hasColumn('task_notes', 'parent_id')) {
            $rules['parent_id'] = ['nullable', 'exists:task_notes,id'];
        }

        $data = $request->validate($rules);

        $payload = [
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'note' => $data['note'],
        ];
        if (Schema::hasColumn('task_notes', 'parent_id') && !empty($data['parent_id'])) {
            $payload['parent_id'] = $data['parent_id'];
        }

        $note = TaskNote::create($payload);

        if ($request->hasFile('attachments') && Schema::hasTable('task_note_attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('task-notes', 'public');
                TaskNoteAttachment::create([
                    'task_note_id' => $note->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        if (class_exists(TaskHistory::class)) {
            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => auth()->id(),
                'action' => 'comment_added',
                'description' => 'Comment added on task',
            ]);
        }

        return back()->with('success', 'Comment posted.');
    }

    public function update(Request $request, TaskNote $note)
    {
        if ($note->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate(['note' => ['required', 'string', 'max:5000']]);
        $update = ['note' => $data['note']];
        if (Schema::hasColumn('task_notes', 'edited_at')) {
            $update['edited_at'] = now();
        }
        $note->update($update);

        return back()->with('success', 'Comment updated.');
    }

    public function destroy(TaskNote $note)
    {
        if ($note->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }
        $note->delete();
        return back()->with('success', 'Comment deleted.');
    }

    public function pin(TaskNote $note)
    {
        if (! auth()->user()->canMonitor()) {
            abort(403);
        }
        if (! Schema::hasColumn('task_notes', 'is_pinned')) {
            return back()->with('error', 'Please run migrations to enable pinning.');
        }
        $note->update(['is_pinned' => ! $note->is_pinned]);
        return back()->with('success', $note->is_pinned ? 'Comment pinned.' : 'Comment unpinned.');
    }

    protected function authorizeAccess(Task $task): void
    {
        $user = auth()->user();
        if (method_exists($user, 'canMonitor') && $user->canMonitor()) {
            return;
        }
        $isAssignee = $task->assignments()->where('assignee_id', $user->id)->where('is_active', true)->exists();
        if (! $isAssignee) {
            abort(403);
        }
    }
}
