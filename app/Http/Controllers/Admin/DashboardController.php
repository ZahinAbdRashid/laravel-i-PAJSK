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
        // 1. Pie Chart: Students distribution by Class (Tingkatan)
        $students = Student::with('teacher')->get();
        
        $classDistribution = [];
        foreach ($students as $student) {
            $className = $student->teacher ? $student->teacher->assigned_class : 'Tiada Kelas';
            if (!isset($classDistribution[$className])) {
                $classDistribution[$className] = 0;
            }
            $classDistribution[$className]++;
        }

        // Format for Chart.js
        $pieChartData = [
            'labels' => array_keys($classDistribution),
            'data' => array_values($classDistribution)
        ];

        // 2. Bar Chart: Activities Submissions & Rejections grouped by Teacher
        $activities = Activity::select(
                'users.name as teacher_name',
                DB::raw('COUNT(*) as total_submissions'),
                DB::raw("SUM(CASE WHEN activities.status = 'rejected' THEN 1 ELSE 0 END) as total_rejected")
            )
            ->join('students', 'activities.student_id', '=', 'students.id')
            ->join('teachers', 'students.teacher_id', '=', 'teachers.id')
            ->join('users', 'teachers.user_id', '=', 'users.id') // Join with users to get actual name
            ->whereNull('activities.deleted_at') // Soft deletes check
            ->groupBy('teachers.id', 'users.name')
            ->get();
            
        $teacherNames = [];
        $totalSubmissions = [];
        $totalRejections = [];
        
        foreach($activities as $activity) {
            $tName = $activity->teacher_name ?: 'Unknown';
            
            $teacherNames[] = $tName;
            $totalSubmissions[] = $activity->total_submissions;
            $totalRejections[] = $activity->total_rejected;
        }

        // Format for Chart.js
        $barChartData = [
            'labels' => $teacherNames,
            'datasets' => [
                [
                    'label' => 'Total Submissions',
                    'data' => $totalSubmissions,
                    'backgroundColor' => 'rgba(79, 70, 229, 0.7)', // Indigo
                    'borderColor' => 'rgb(79, 70, 229)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Rejected Submissions',
                    'data' => $totalRejections,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)', // Red
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 1
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'pieChart' => $pieChartData,
            'barChart' => $barChartData
        ]);
    }
}

