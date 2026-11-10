<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringCompletion extends Model
{
    protected $fillable = [
        'user_id', 'taskable_type', 'taskable_id', 'period_key', 'frequency', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taskable()
    {
        return $this->morphTo();
    }

    public static function periodKey(string $frequency, $date = null): string
    {
        $date = $date ? \Carbon\Carbon::parse($date) : now();

        return match ($frequency) {
            'daily' => $date->format('Y-m-d'),
            'weekly' => $date->format('o') . '-W' . $date->format('W'),
            'monthly' => $date->format('Y-m'),
            default => $date->format('Y-m-d'),
        };
    }
}
