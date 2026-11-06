<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSubtask extends Model
{
    protected $fillable = [
        'task_id', 'assignee_id', 'title', 'description', 'status',
        'deadline', 'sort_order', 'completed_at', 'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'completed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
