<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTransferRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_assignment_id',
        'requested_by',
        'requested_new_assignee_id',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_note',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function newAssignee()
    {
        return $this->belongsTo(User::class, 'requested_new_assignee_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
