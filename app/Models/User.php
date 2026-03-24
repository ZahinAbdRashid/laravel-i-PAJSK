<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'ic_number', 'name', 'email', 'password', 'role', 
        'phone', 'gender'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Add this method for authentication by IC Number
    public function username()
    {
        return 'ic_number';
    }

    // RELATIONSHIPS
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    // Get role-specific data
    public function getRoleData()
    {
        if ($this->isAdmin()) {
            return ['type' => 'admin', 'name' => $this->name];
        }
        
        if ($this->isTeacher()) {
            return [
                'type' => 'teacher',
                'name' => $this->name,
                'teacher' => $this->teacher,
                'class' => $this->teacher->assigned_class ?? null
            ];
        }
        
        if ($this->isStudent()) {
            return [
                'type' => 'student',
                'name' => $this->name,
                'student' => $this->student,
                'class' => $this->student->teacher->assigned_class ?? null
            ];
        }
        
        return null;
    }
}