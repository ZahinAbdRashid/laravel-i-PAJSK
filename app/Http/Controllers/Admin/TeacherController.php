<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    // Display a listing of teachers.
    public function index()
    {
        $teachers = Teacher::with('user')->latest()->paginate(15);
        
        return view('admin.manage-teachers', compact('teachers'));
    }

    // Show the form for creating a new teacher.
    public function create()
    {
        return view('admin.teachers.create');
    }

    // Store a newly created teacher in storage.
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'required|string|unique:users,ic_number',
            'staff_id' => 'required|string|unique:teachers,staff_id',
            'subject' => 'required|string',
            'assigned_class' => 'required|in:alpha,delta,omega',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'gender' => 'required|in:male,female',
        ]);

        // Create user
        $user = User::create([
            'ic_number' => $request->ic_number,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('admin123'), // Default password
            'role' => 'teacher',
            'phone' => $request->phone,
            'gender' => $request->gender,
        ]);

        // Create teacher
        Teacher::create([
            'user_id' => $user->id,
            'staff_id' => $request->staff_id,
            'subject' => $request->subject,
            'assigned_class' => $request->assigned_class,
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully!');
    }

    // Display the specified teacher.
    public function show(Teacher $teacher)
    {
        $teacher->load('user', 'students.user');
        return view('admin.teachers.show', compact('teacher'));
    }

    // Show the form for editing the specified teacher.
    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    // Update the specified teacher in storage.
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ic_number' => 'required|string|unique:users,ic_number,' . $teacher->user_id,
            'staff_id' => 'required|string|unique:teachers,staff_id,' . $teacher->id,
            'subject' => 'required|string',
            'assigned_class' => 'required|in:alpha,delta,omega',
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'phone' => 'required|string',
            'gender' => 'required|in:male,female',
        ]);

        // Update user
        $teacher->user->update([
            'ic_number' => $request->ic_number,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
        ]);

        // Update teacher
        $teacher->update([
            'staff_id' => $request->staff_id,
            'subject' => $request->subject,
            'assigned_class' => $request->assigned_class,
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully!');
    }

    // Remove the specified teacher from storage.
    public function destroy(Teacher $teacher, Request $request)
    {
        try {
            // Check if teacher has students
            $studentsCount = $teacher->students()->count();
            $confirmDelete = $request->input('confirm_delete', false);
            
            // If teacher has students and not confirmed, show confirmation message
            if ($studentsCount > 0 && !$confirmDelete) {
                return redirect()->route('admin.teachers.index')
                    ->with('confirm_delete', [
                        'teacher_id' => $teacher->id,
                        'students_count' => $studentsCount,
                        'message' => "This teacher has {$studentsCount} assigned student(s). Deleting this teacher will also delete all students and their data. Are you sure you want to continue?"
                    ]);
            }

            // Get all students before deleting
            $students = $teacher->students()->with('user')->get();
            
            // Delete all students and their users first
            foreach ($students as $student) {
                // Delete student's activities, marks, etc. (cascade will handle)
                // Delete student user
                if ($student->user) {
                    $student->user->delete();
                }
                // Force delete student
                $student->forceDelete();
            }

            // Get user before deleting teacher
            $user = $teacher->user;
            
            // Use forceDelete to permanently delete teacher (bypass soft delete)
            $teacher->forceDelete();
            
            // Delete user if teacher was successfully deleted
            if ($user) {
                $user->delete();
            }
            
            $message = $studentsCount > 0 
                ? "Teacher and {$studentsCount} student(s) deleted successfully!"
                : 'Teacher deleted successfully!';
            
            return redirect()->route('admin.teachers.index')
                ->with('success', $message);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle foreign key constraint errors
            if ($e->getCode() == 23000) {
                return redirect()->route('admin.teachers.index')
                    ->with('error', 'Cannot delete teacher. This teacher has related records that cannot be deleted. Please check the database constraints.');
            }
            return redirect()->route('admin.teachers.index')
                ->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('admin.teachers.index')
                ->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }
}