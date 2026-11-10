<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonalTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'deadline',
        'priority',
        'frequency',
        'recurrence_weekday',
        'recurrence_monthday',
        'reminder_days_before',
        'status',
        'completed_at',
        'attachment',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completions()
    {
        return $this->morphMany(RecurringCompletion::class, 'taskable');
    }
}
