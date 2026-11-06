<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskNoteAttachment extends Model
{
    protected $fillable = [
        'task_note_id', 'file_path', 'original_name', 'mime_type', 'file_size',
    ];

    public function note()
    {
        return $this->belongsTo(TaskNote::class, 'task_note_id');
    }
}
