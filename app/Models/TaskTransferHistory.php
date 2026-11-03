<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTransferHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'old_assignment_id',
        'previous_assignee_id',
        'new_assignee_id',
        'transferred_by',
        'transfer_type',
        'reason',
        'notes',
        'transferred_at',
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function oldAssignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'old_assignment_id');
    }

    public function previousAssignee()
    {
        return $this->belongsTo(User::class, 'previous_assignee_id');
    }

    public function newAssignee()
    {
        return $this->belongsTo(User::class, 'new_assignee_id');
    }

    public function transferredBy()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
