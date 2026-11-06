<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssistanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'task_assignment_id',
        'requester_id',
        'description',
        'status',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_assistance_users', 'assistance_request_id', 'user_id')
            ->withPivot(['status', 'helper_note', 'requester_note', 'submitted_at', 'responded_at'])
            ->withTimestamps();
    }
}
