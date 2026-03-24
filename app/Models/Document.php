<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'activity_id', 'filename', 'original_name', 'path',
        'mime_type', 'size'
    ];

    // RELATIONSHIPS
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}