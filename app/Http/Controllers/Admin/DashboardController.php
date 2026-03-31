<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Display the student data backup page
     */
    public function studentBackup()
    {
        return view('admin.students_backup');
    }

    /**
     * API endpoint to get all students for admin
     */
    public function getAllStudents()
    {
        $students = Student::with(['user', 'teacher', 'currentMark'])
            ->whereHas('user')
            ->get()
            ->map(function ($student) {
                $mark = $student->currentMark;
                $teacher = $student->teacher;
                
                return [
                    'id' => $student->id,
                    'name' => $student->user ? $student->user->name : 'N/A',
                    'ic' => $student->user ? $student->user->ic_number : 'N/A',
                    'gender' => $student->user ? $student->user->gender : 'N/A',
                    'class' => $teacher ? $teacher->assigned_class : 'N/A',
                    'academicSession' => $student->academic_session ?? '',
                    'semester' => $student->semester ?? '',
                    'sports' => $student->sports ?? '',
                    'club' => $student->club ?? '',
                    'uniform' => $student->uniform ?? '',
                    'position' => $student->position ?? '',
                    'totalMarks' => $mark ? ($mark->total ?? 0) : 0
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students->values()->all()
        ]);
    }

    /**
     * API endpoint to get chart data for the dashboard
     */
    public function getChartData()
    {
        // 1. Gather all students with user and currentMark to compute Gender per class and Grades
        $students = Student::with(['teacher', 'user', 'currentMark'])->get();
        
        $classDistribution = [];
        $gradesCount = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

        foreach ($students as $student) {
            $className = $student->teacher ? $student->teacher->assigned_class : 'Tiada Kelas';
            if (!isset($classDistribution[$className])) {
                $classDistribution[$className] = [
                    'className' => $className,
                    'male' => 0,
                    'female' => 0,
                    'monthlyStatus' => [
                        'approved' => array_fill(1, 12, 0),
                        'rejected' => array_fill(1, 12, 0)
                    ]
                ];
            }
            
            // Gender count
            $gender = $student->user ? $student->user->gender : 'Unknown';
            if (in_array(strtolower($gender), ['male', 'lelaki'])) {
                $classDistribution[$className]['male']++;
            } elseif (in_array(strtolower($gender), ['female', 'perempuan'])) {
                $classDistribution[$className]['female']++;
            }

            // Grade logic (based on total points)
            $score = $student->currentMark ? (int)$student->currentMark->total : 0;
            if ($score >= 80) $gradesCount['A']++;
            elseif ($score >= 70) $gradesCount['B']++;
            elseif ($score >= 60) $gradesCount['C']++;
            elseif ($score >= 50) $gradesCount['D']++;
            else $gradesCount['E']++;
        }

        // 2. Activities for Monthly Status per class
        $activities = Activity::select(
            'activities.status',
            'activities.created_at',
            'teachers.assigned_class'
        )
        ->join('students', 'activities.student_id', '=', 'students.id')
        ->join('teachers', 'students.teacher_id', '=', 'teachers.id')
        ->whereNull('activities.deleted_at')
        ->get();

        foreach ($activities as $activity) {
            $className = $activity->assigned_class ?: 'Tiada Kelas';
            if (isset($classDistribution[$className]) && $activity->created_at) {
                $month = (int)$activity->created_at->format('n'); // 1-12
                if ($activity->status === 'approved') {
                    $classDistribution[$className]['monthlyStatus']['approved'][$month]++;
                } elseif ($activity->status === 'rejected') {
                    $classDistribution[$className]['monthlyStatus']['rejected'][$month]++;
                }
            }
        }

        // Prepare structured data for frontend
        $classNames = array_keys($classDistribution);
        $pieCharts = [];
        $barCharts = [];

        foreach ($classDistribution as $className => $data) {
            $pieCharts[] = [
                'className' => $className,
                'male' => $data['male'],
                'female' => $data['female']
            ];
            
            $barCharts[] = [
                'className' => $className,
                // Flatten to simple 12-element array
                'approved' => array_values($data['monthlyStatus']['approved']),
                'rejected' => array_values($data['monthlyStatus']['rejected'])
            ];
        }

        return response()->json([
            'success' => true,
            'classes' => $classNames,
            'pieCharts' => $pieCharts,
            'barCharts' => $barCharts,
            'gradeChart' => [
                'labels' => ['A', 'B', 'C', 'D', 'E'],
                'data' => array_values($gradesCount) // [A,B,C,D,E]
            ]
        ]);
    }
}

