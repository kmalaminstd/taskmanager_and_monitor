<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_assignment_id',
        'submitted_by',
        'completion_details',
        'completion_note',
        'status',
        'reviewed_by',
        'reviewed_at',
        'return_note',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
