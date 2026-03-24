<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'student_id', 'type', 'name', 'level', 'achievement',
        'activity_date','status', 'teacher_comment',
        'approved_by', 'approved_at', 'student_notified', 'appeal_comment'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'approved_at' => 'datetime'
    ];

    // RELATIONSHIPS
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Teacher::class, 'approved_by');
    }

    // SCOPES
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Calculate points based on level and achievement
    public function calculatePoints()
    {
        $levelPoints = [
            'school' => 2,
            'district' => 4,
            'state' => 6,
            'national' => 8,
            'international' => 10
        ];

        $achievementMultiplier = [
            'participation' => 1,
            'third' => 1.5,
            'second' => 2,
            'first' => 3
        ];

        $points = $levelPoints[$this->level] ?? 2;
        $multiplier = $achievementMultiplier[$this->achievement] ?? 1;

        return $points * $multiplier;
    }
}