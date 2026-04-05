<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    protected $fillable = [
        'student_id', 'uniform', 'club', 'sport', 'competition', 
        'extra', 'total', 'grade', 'is_manual_override', 
        'last_updated_by', 'override_reason'
    ];

    // RELATIONSHIPS
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(Teacher::class, 'last_updated_by');
    }

    // Calculate grade from total
    public static function calculateGrade($total)
    {
        if ($total >= 80) return 'A';
        if ($total >= 60) return 'B';
        if ($total >= 40) return 'C';
        if ($total >= 20) return 'D';
        return 'E';
    }

    // Calculate total from components
    public function calculateTotal()
    {
        $total = $this->uniform + $this->club + $this->sport + 
                    $this->competition + $this->extra;
        
        return min($total, 100); // Cap at 100
    }
}