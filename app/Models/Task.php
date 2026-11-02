<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'category_id',
        'priority',
        'deadline',
        'task_type',
        'frequency',
        'instructions',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function activeAssignments()
    {
        return $this->hasMany(TaskAssignment::class)->where('is_active', true);
    }

    public function notes()
    {
        return $this->hasMany(TaskNote::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function transferHistories()
    {
        return $this->hasMany(TaskTransferHistory::class);
    }

    public function assistanceRequests()
    {
        return $this->hasMany(TaskAssistanceRequest::class);
    }
}
