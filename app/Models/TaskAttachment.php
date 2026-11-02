<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'task_assignment_id',
        'task_submission_id',
        'uploaded_by',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignment()
    {
        return $this->belongsTo(TaskAssignment::class, 'task_assignment_id');
    }

    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class, 'task_submission_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
