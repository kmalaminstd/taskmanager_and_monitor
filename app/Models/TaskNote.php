<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'task_assignment_id',
        'user_id',
        'note',
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
}
