<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\TaskAttachment;
use App\Models\TaskHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Notifications\TaskAssignedNotification;
// use App\Models\User;

class TaskService
{
    public function createTask(array $data, User $creator, ?UploadedFile $attachment = null): Task
    {
        return DB::transaction(function () use ($data, $creator, $attachment) {
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'created_by' => $creator->id,
                'category_id' => $data['category_id'] ?? null,
                'priority' => $data['priority'],
                'deadline' => $data['deadline'] ?? null,
                'task_type' => $data['task_type'],
                'frequency' => $data['frequency'] ?? null,
                'instructions' => $data['instructions'] ?? null,
                'status' => 'active',
            ]);

            // Create independent assignments for each assignee
            foreach ($data['assignee_ids'] as $assigneeId) {
                $assignment = TaskAssignment::create([
                    'task_id' => $task->id,
                    'assignee_id' => $assigneeId,
                    'assigned_by' => $creator->id,
                    'status' => 'pending',
                    'assigned_at' => now(),
                    'deadline' => $data['deadline'] ?? null,
                    'priority' => $data['priority'],
                    'is_active' => true,
                ]);

                // Notify assignee
                $assignee = User::find($assigneeId);
                if ($assignee) {
                    $assignee->notify(new TaskAssignedNotification($assignment));
                }
            }

            if ($attachment) {
                $this->storeAttachment($task, $attachment, $creator);
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => $creator->id,
                'action' => 'created',
                'new_values' => $task->toArray(),
                'description' => 'Task created and assigned to ' . count($data['assignee_ids']) . ' user(s)',
            ]);

            return $task->load(['assignments.assignee', 'category', 'creator']);
        });
    }

    public function updateTask(Task $task, array $data, User $actor, ?UploadedFile $attachment = null): Task
    {
        return DB::transaction(function () use ($task, $data, $actor, $attachment) {
            $oldValues = $task->toArray();

            $task->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'priority' => $data['priority'],
                'deadline' => $data['deadline'] ?? null,
                'task_type' => $data['task_type'],
                'frequency' => $data['frequency'] ?? null,
                'instructions' => $data['instructions'] ?? null,
            ]);

            // Handle assignee changes if provided
            if (isset($data['assignee_ids'])) {
                $this->syncAssignees($task, $data['assignee_ids'], $actor);
            }

            if ($attachment) {
                $this->storeAttachment($task, $attachment, $actor);
            }

            TaskHistory::create([
                'task_id' => $task->id,
                'user_id' => $actor->id,
                'action' => 'updated',
                'old_values' => $oldValues,
                'new_values' => $task->fresh()->toArray(),
                'description' => 'Task details updated',
            ]);

            return $task->fresh(['assignments.assignee', 'category', 'creator']);
        });
    }

    protected function syncAssignees(Task $task, array $newAssigneeIds, User $actor): void
    {
        $currentActive = $task->assignments()->where('is_active', true)->pluck('assignee_id')->toArray();
        $toAdd = array_diff($newAssigneeIds, $currentActive);
        $toRemove = array_diff($currentActive, $newAssigneeIds);

        // Deactivate removed
        if (!empty($toRemove)) {
            $task->assignments()
                ->where('is_active', true)
                ->whereIn('assignee_id', $toRemove)
                ->update(['is_active' => false]);
        }

        // Add new
        foreach ($toAdd as $assigneeId) {
            TaskAssignment::create([
                'task_id' => $task->id,
                'assignee_id' => $assigneeId,
                'assigned_by' => $actor->id,
                'status' => 'pending',
                'assigned_at' => now(),
                'deadline' => $task->deadline,
                'priority' => $task->priority,
                'is_active' => true,
            ]);
        }
    }

    protected function storeAttachment(Task $task, UploadedFile $file, User $uploader): void
    {
        $path = $file->store('task-attachments/' . $task->id, 'public');

        TaskAttachment::create([
            'task_id' => $task->id,
            'uploaded_by' => $uploader->id,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    public function getFilteredAssignments(array $filters = [])
    {
        $query = TaskAssignment::with(['task.category', 'assignee', 'assignedBy'])
            ->where('is_active', true);

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'overdue') {
                $query->whereNotIn('status', ['submitted', 'completed', 'cancelled'])
                      ->where('deadline', '<', now());
            } else {
                $query->where('status', $filters['status']);
            }
        }

        if (!empty($filters['assignee_id'])) {
            $query->where('assignee_id', $filters['assignee_id']);
        }

        if (!empty($filters['priority'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('priority', $filters['priority'])
                  ->orWhereHas('task', fn($t) => $t->where('priority', $filters['priority']));
            });
        }

        if (!empty($filters['search'])) {
            $query->whereHas('task', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('assigned_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('assigned_at', '<=', $filters['date_to']);
        }

        $sort = $filters['sort'] ?? 'deadline';
        $direction = $filters['direction'] ?? 'asc';

        if (in_array($sort, ['deadline', 'assigned_at', 'priority', 'status'])) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('deadline', 'asc');
            // $query->latest();
        }

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }
}
