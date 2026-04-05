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
        // Official Malaysian PAJSK Rubric for Achievement (Pencapaian)
        $rubric = [
            'international' => [
                'first' => 20,
                'second' => 19,
                'third' => 18,
                'participation' => 15
            ],
            'national' => [
                'first' => 17,
                'second' => 16,
                'third' => 15,
                'participation' => 12
            ],
            'state' => [
                'first' => 14,
                'second' => 13,
                'third' => 12,
                'participation' => 10
            ],
            'district' => [
                'first' => 11,
                'second' => 10,
                'third' => 9,
                'participation' => 8
            ],
            'school' => [
                'first' => 8,
                'second' => 7,
                'third' => 6,
                'participation' => 5
            ]
        ];

        $level = $this->level ?? 'school';
        $achievement = $this->achievement ?? 'participation';

        // Assuming all Activity Types (Uniform, Club, Sport) use the same standard PAJSK rubric for achievement.
        return $rubric[$level][$achievement] ?? 5; // Default to minimum if not found
    }
}