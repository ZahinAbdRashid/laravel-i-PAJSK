<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            abort(403, 'Teacher profile not found');
        }
        
        $students = $teacher->students()
            ->with('user')
            ->whereHas('user') // Only get students that have user
            ->get();
        
        return view('teacher.manage-students', [
            'students' => $students,
            'teacher' => $teacher
        ]);
    }

    // Export a PDF report of the class's PAJSK marks.
    public function exportReport()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            abort(403, 'Unauthorized access.');
        }

        // Fetch all students for this teacher with user profiles and PAJSK marks
        $students = Student::where('teacher_id', $teacher->id)
            ->with(['user', 'marks'])
            ->get()
            ->sortBy(function ($student) {
                return $student->user->name ?? '';
            });

        // Use base64 encoding for the logo so it renders properly in DomPDF
        $logoPath = public_path('images/logo sekolah.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $pdf = \PDF::loadView('teacher.students.report', compact('teacher', 'students', 'logoBase64'));
        
        // Use landscape logic because there are many columns (Marks breakdown)
        $pdf->setPaper('a4', 'landscape');
        
        $filename = 'Class_PAJSK_Report_' . ($teacher->assigned_class ? str_replace(' ', '_', $teacher->assigned_class) : 'All') . '_' . date('Y') . '.pdf';

        return $pdf->download($filename);
    }

    // Show the form for creating a new resource.
    public function create()
    {
        $teacher = Auth::user()->teacher;
        return view('teacher.students.create', [
            'teacher' => $teacher,
            'academicSessions' => $this->getAcademicSessions(),
            'semesters' => ['1', '2', '3'],
            'sportsOptions' => $this->getSportsOptions(),
            'clubOptions' => $this->getClubOptions(),
            'uniformOptions' => $this->getUniformOptions(),
            'positionOptions' => $this->getPositionOptions()
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'ic_number' => 'required|unique:users,ic_number|regex:/^\d{6}-\d{2}-\d{4}$/',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'academic_session' => 'required|string',
            'semester' => 'required|in:1,2,3',
            'email' => 'nullable|email|unique:users,email',
        ]);

        // Create user
        $user = User::create([
            'ic_number' => $request->ic_number,
            'name' => $request->name,
            'email' => $request->email ?? $request->ic_number . '@student.edu.my',
            'password' => Hash::make('admin123'), // Default password
            'role' => 'student',
            'phone' => $request->phone,
            'gender' => $request->gender,
        ]);

        // Create student
        $student = Student::create([
            'user_id' => $user->id,
            'teacher_id' => Auth::user()->teacher->id,
            'academic_session' => $request->academic_session,
            'semester' => $request->semester,
            'sports' => $request->sports,
            'club' => $request->club,
            'uniform' => $request->uniform,
            'position' => $request->position,
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student added successfully! Default password: admin123',
                'student' => $student->load('user')
            ]);
        }

        return redirect()->route('teacher.students.index')
            ->with('success', 'Student added successfully! Default password: admin123');
    }

    // Display the specified resource.
    public function show(string $id)
    {
        $student = Student::where('teacher_id', Auth::user()->teacher->id)
                            ->with(['user', 'marks', 'activities'])
                            ->findOrFail($id);
        
        return view('teacher.students.show', compact('student'));
    }

    // Show the form for editing the specified resource.
    public function edit(string $id)
    {
        $student = Student::where('teacher_id', Auth::user()->teacher->id)
                            ->with('user')
                            ->findOrFail($id);
        
        return view('teacher.students.edit', [
            'student' => $student,
            'academicSessions' => $this->getAcademicSessions(),
            'semesters' => ['1', '2', '3'],
            'sportsOptions' => $this->getSportsOptions(),
            'clubOptions' => $this->getClubOptions(),
            'uniformOptions' => $this->getUniformOptions(),
            'positionOptions' => $this->getPositionOptions()
        ]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $student = Student::where('teacher_id', Auth::user()->teacher->id)
                            ->with('user')
                            ->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'required|regex:/^\d{6}-\d{2}-\d{4}$/|unique:users,ic_number,' . $student->user_id,
            'gender' => 'required|in:male,female',
            'academic_session' => 'required|string',
            'semester' => 'required|in:1,2,3',
            'email' => 'nullable|email|unique:users,email,' . $student->user_id,
        ]);

        // Update user
        $student->user->update([
            'ic_number' => $request->ic_number,
            'name' => $request->name,
            'email' => $request->email ?? $student->user->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
        ]);

        // Update student
        $student->update([
            'academic_session' => $request->academic_session,
            'semester' => $request->semester,
            'sports' => $request->sports,
            'club' => $request->club,
            'uniform' => $request->uniform,
            'position' => $request->position,
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully!',
                'student' => $student->load('user')
            ]);
        }

        return redirect()->route('teacher.students.index')
            ->with('success', 'Student updated successfully!');
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $student = Student::where('teacher_id', Auth::user()->teacher->id)
                            ->with('user')
                            ->findOrFail($id);
        
        // Delete user (will cascade delete student)
        $student->user->delete();

        // Return JSON response for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully!'
            ]);
        }

        return redirect()->route('teacher.students.index')
            ->with('success', 'Student deleted successfully!');
    }

    // Update student marks
    public function updateMarks(Request $request, $id)
    {
        $student = Student::where('teacher_id', Auth::user()->teacher->id)
                            ->findOrFail($id);
        
        $request->validate([
            'uniform' => 'required|integer|min:0|max:20',
            'club' => 'required|integer|min:0|max:20',
            'sport' => 'required|integer|min:0|max:20',
            'competition' => 'required|integer|min:0|max:40',
            'extra' => 'nullable|integer|min:0|max:100',
            'override_reason' => 'nullable|string|max:500'
        ]);

        $total = $request->uniform + $request->club + $request->sport + 
                    $request->competition + ($request->extra ?? 0);
        $total = min($total, 100); // Cap at 100

        $grade = $this->calculateGrade($total);

        // Update or create marks
        $student->marks()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'uniform' => $request->uniform,
                'club' => $request->club,
                'sport' => $request->sport,
                'competition' => $request->competition,
                'extra' => $request->extra ?? 0,
                'total' => $total,
                'grade' => $grade,
                'is_manual_override' => true,
                'last_updated_by' => Auth::user()->teacher->id,
                'override_reason' => $request->override_reason
            ]
        );

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Marks updated successfully!',
                'marks' => $student->marks
            ]);
        }

        return redirect()->route('teacher.students.show', $student->id)
            ->with('success', 'Marks updated successfully!');
    }

    // Helper methods
    private function getAcademicSessions()
    {
        return [
            '2023/2024',
            '2024/2025',
            '2025/2026',
            '2026/2027',
            '2027/2028'
        ];
    }

    private function getSportsOptions()
    {
        return [
            '' => 'None',
            'badminton' => 'Badminton',
            'football' => 'Football',
            'basketball' => 'Basketball',
            'netball' => 'Netball',
            'athletics' => 'Athletics',
            'swimming' => 'Swimming',
            'tennis' => 'Tennis',
            'bola jaring' => 'Bola Jaring',
            'athletics' => 'Athletics'
        ];
    }

    private function getClubOptions()
    {
        return [
            '' => 'None',
            'alam_sekitar' => 'Alam Sekitar',
            'ict_club' => 'ICT Club',
            'english_society' => 'English Language Society',
            'science_club' => 'Science Club',
            'math_club' => 'Mathematics Club',
            'art_club' => 'Art & Culture Club',
            'debate_club' => 'Debate Club',
            'alam sekitar' => 'Alam Sekitar',
            'ict' => 'ICT Club',
            'english' => 'English Language Society',
            'science' => 'Science Club'
        ];
    }

    private function getUniformOptions()
    {
        return [
            '' => 'None',
            'pengakap' => 'Pengakap',
            'kadet_polis' => 'Kadet Polis',
            'kadet_bomba' => 'Kadet Bomba',
            'puteri_islam' => 'Puteri Islam',
            'kadet_remaja' => 'Kadet Remaja Sekolah',
            'bulan_sabit_merah' => 'Bulan Sabit Merah',
            'pengakap' => 'Pengakap',
            'kadet polis' => 'Kadet Polis',
            'kadet bomba' => 'Kadet Bomba',
            'puteri islam' => 'Puteri Islam'
        ];
    }

    private function getPositionOptions()
    {
        return [
            '' => 'None',
            'ketua_kelas' => 'Ketua Kelas',
            'penolong_ketua_kelas' => 'Penolong Ketua Kelas',
            'pengawas_sekolah' => 'Pengawas Sekolah',
            'penolong_pengawas' => 'Penolong Pengawas',
            'ketua_persatuan' => 'Ketua Persatuan',
            'naib_ketua_persatuan' => 'Naib Ketua Persatuan',
            'setiausaha' => 'Setiausaha',
            'bendahari' => 'Bendahari',
            'pengawas sekolah' => 'Pengawas Sekolah',
            'ketua kelas' => 'Ketua Kelas',
            'penolong ketua kelas' => 'Penolong Ketua Kelas',
            'ketua persatuan' => 'Ketua Persatuan'
        ];
    }

    private function calculateGrade($score)
    {
        if ($score >= 80) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        if ($score >= 20) return 'D';
        return 'E';
    }
    
    // API endpoint to get all students for current teacher
    public function apiIndex()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }
        
        $students = $teacher->students()
            ->with(['user', 'currentMark'])
            ->get()
            ->filter(function ($student) {
                return $student->user !== null;
            })
            ->map(function ($student) {
                $mark = $student->currentMark;
                return [
                    'id' => $student->id,
                    'name' => $student->user ? $student->user->name : 'N/A',
                    'icNumber' => $student->user ? $student->user->ic_number : 'N/A',
                    'gender' => $student->user ? $student->user->gender : 'N/A',
                    'academicSession' => $student->academic_session ?? '',
                    'semester' => $student->semester ?? '',
                    'sportsGames' => $student->sports ?? '',
                    'clubAssociation' => $student->club ?? '',
                    'uniformUnit' => $student->uniform ?? '',
                    'position' => $student->position ?? '',
                    'marks' => $mark ? [
                        'uniform' => $mark->uniform ?? 0,
                        'club' => $mark->club ?? 0,
                        'sport' => $mark->sport ?? 0,
                        'competition' => $mark->competition ?? 0,
                        'extra' => $mark->extra ?? 0,
                        'total' => $mark->total ?? 0,
                        'grade' => $mark->grade ?? 'E'
                    ] : null
                ];
            });
        
        return response()->json([
            'success' => true,
            'students' => $students->values()->all()
        ]);
    }
    
    // API endpoint to update marks
    public function apiUpdateMarks(Request $request, $id)
    {
        return $this->updateMarks($request, $id);
    }
}