<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'teacher_id', 'academic_session', 'semester',
        'sports', 'club', 'uniform', 'position'
    ];

    // RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function currentMark()
    {
        return $this->hasOne(Mark::class)->latest();
    }

    // HELPER METHODS
    public function pendingActivities()
    {
        return $this->activities()->where('status', 'pending');
    }

    public function approvedActivities()
    {
        return $this->activities()->where('status', 'approved');
    }

    // Accessor for class
    public function getClassAttribute()
    {
        return $this->teacher->assigned_class ?? 'N/A';
    }

    // Scope for class filtering
    public function scopeByClass($query, $class)
    {
        return $query->whereHas('teacher', function($q) use ($class) {
            $q->where('assigned_class', $class);
        });
    }
}