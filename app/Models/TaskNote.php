<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskNote extends Model
{
    protected $fillable = [
        'task_id', 'task_assignment_id', 'user_id', 'parent_id',
        'note', 'is_pinned', 'edited_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'edited_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(TaskNote::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(TaskNote::class, 'parent_id')->orderBy('created_at');
    }

    public function attachments()
    {
        return $this->hasMany(TaskNoteAttachment::class);
    }
}
