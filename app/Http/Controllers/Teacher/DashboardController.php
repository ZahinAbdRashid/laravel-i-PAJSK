<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Activity;
use App\Models\Mark;

class DashboardController extends Controller
{
    /**
     * Display teacher dashboard.
     */
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Get students in teacher's class
        $students = Student::where('teacher_id', $teacher->id)
            ->with(['user', 'marks'])
            ->get();
        
        // Get total pending activities for stats card
        $pendingCount = Activity::whereIn('student_id', $students->pluck('id'))
            ->where('status', 'pending')
            ->count();
        
        // Get gender statistics
        $maleCount = $students->where('user.gender', 'male')->count();
        $femaleCount = $students->where('user.gender', 'female')->count();
        
        // Get grade distribution
        $gradeDistribution = $this->getGradeDistribution($students);

        // Get Top 10 Students based on total marks
        $topStudents = Student::where('teacher_id', $teacher->id)
            ->whereHas('marks')
            ->with(['user', 'marks'])
            ->get()
            ->sortByDesc(function ($student) {
                return $student->marks->first()->total ?? 0;
            })
            ->take(10);

        // Get Underperforming Students (Grade D or E)
        $needsAttention = Student::where('teacher_id', $teacher->id)
            ->whereHas('marks', function ($query) {
                $query->whereIn('grade', ['D', 'E']);
            })
            ->with(['user', 'marks'])
            ->get()
            ->sortBy(function ($student) {
                return $student->marks->first()->total ?? 100; // Sort lowest first
            });
            
        // Get Monthly Statistics
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $monthlyApproved = Activity::whereIn('student_id', $students->pluck('id'))
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();
            
        $monthlyRejected = Activity::whereIn('student_id', $students->pluck('id'))
            ->where('status', 'rejected')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();
        
        return view('teacher.dashboard', compact(
            'students',
            'pendingCount',
            'maleCount',
            'femaleCount',
            'gradeDistribution',
            'topStudents',
            'needsAttention',
            'monthlyApproved',
            'monthlyRejected'
        ));
    }

    /**
     * Get grade distribution for teacher's class.
     */
    private function getGradeDistribution($students)
    {
        $grades = [
            'A' => 0,
            'B' => 0,
            'C' => 0,
            'D' => 0,
            'E' => 0
        ];
        
        foreach ($students as $student) {
            if ($student->marks && $student->marks->isNotEmpty()) {
                $mark = $student->marks->first(); 
                if ($mark && $mark->grade) {
                    $grade = $mark->grade;
                    if (isset($grades[$grade])) {
                        $grades[$grade]++;
                    }
                }
            }
        }
        
        return $grades;
    }
}