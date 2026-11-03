<?php

namespace App\Notifications;

use App\Models\TaskAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(public TaskAssignment $assignment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_assigned',
            'title' => 'New Task Assigned',
            'message' => 'You have been assigned: ' . ($this->assignment->task->title ?? 'a task'),
            'task_id' => $this->assignment->task_id,
            'assignment_id' => $this->assignment->id,
            'url' => route('assignee.tasks.show', $this->assignment->id),
        ];
    }
}
