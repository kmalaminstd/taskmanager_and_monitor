<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class, 'assignee_id');
    }

    public function activeAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'assignee_id')->where('is_active', true);
    }

    public function assignedByMe()
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_by');
    }

    public function personalTasks()
    {
        return $this->hasMany(PersonalTask::class);
    }

    public function notes()
    {
        return $this->hasMany(TaskNote::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'submitted_by');
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAssignee(): bool
    {
        return $this->role === 'assignee';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
