<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'staff_id', 'subject', 'assigned_class'
    ];

    protected $casts = [
        'assigned_class' => 'string'
    ];

    // RELATIONSHIPS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function reviewedActivities()
    {
        return $this->hasMany(Activity::class, 'approved_by');
    }

    public function marksUpdated()
    {
        return $this->hasMany(Mark::class, 'last_updated_by');
    }

    // Helper methods
    public function getClassStudents()
    {
        return $this->students()->with('user')->get();
    }

    public function getPendingActivitiesCount()
    {
        return Activity::whereIn('student_id', $this->students()->pluck('id'))
            ->pending()
            ->count();
    }
}