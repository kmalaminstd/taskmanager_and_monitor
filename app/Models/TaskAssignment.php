<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TaskAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_id',
        'assignee_id',
        'assigned_by',
        'status',
        'assigned_at',
        'started_at',
        'submitted_at',
        'approved_at',
        'completed_at',
        'deadline',
        'priority',
        'is_active',
        'admin_notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function latestSubmission()
    {
        return $this->hasOne(TaskSubmission::class)->latestOfMany();
    }

    public function notes()
    {
        return $this->hasMany(TaskNote::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function extensionRequests()
    {
        return $this->hasMany(TaskExtensionRequest::class);
    }

    public function transferRequests()
    {
        return $this->hasMany(TaskTransferRequest::class);
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class);
    }

    // Business logic helpers
    public function isOverdue(): bool
    {
        if (!$this->deadline) {
            return false;
        }

        return $this->deadline->isPast()
            && !in_array($this->status, ['submitted', 'completed', 'cancelled']);
    }

    public function isLateCompleted(): bool
    {
        if (!$this->deadline || !$this->submitted_at) {
            return false;
        }

        return $this->submitted_at->gt($this->deadline);
    }

    public function isOnTime(): bool
    {
        if (!$this->deadline || !$this->submitted_at) {
            return false;
        }

        return $this->submitted_at->lte($this->deadline);
    }

    public function getEffectiveDeadlineAttribute()
    {
        return $this->deadline ?? $this->task?->deadline;
    }

    public function getEffectivePriorityAttribute()
    {
        return $this->priority ?? $this->task?->priority ?? 'medium';
    }
}
