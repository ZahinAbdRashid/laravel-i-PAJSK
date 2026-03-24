<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Student\ActivityController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\ApprovalController;
use App\Http\Controllers\Teacher\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', function () {
    return redirect()->route('login.form', ['role' => 'admin']);
});

// Authentication Routes
Route::prefix('login')->group(function () {
    Route::get('/{role}', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('/', [LoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Profile Routes (Change Password) - FOR ALL ROLES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ============================================================
// PROTECTED ROUTES WITH MANUAL ROLE CHECKS
// ============================================================

// Protected Admin Routes - MANUAL CHECKS
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->role !== 'admin') {
            abort(403, 'Admin access required. Your role: ' . $user->role);
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Teacher Management CRUD
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class)
        ->names([
            'index' => 'admin.teachers.index',
            'create' => 'admin.teachers.create',
            'store' => 'admin.teachers.store',
            'show' => 'admin.teachers.show',
            'edit' => 'admin.teachers.edit',
            'update' => 'admin.teachers.update',
            'destroy' => 'admin.teachers.destroy'
        ]);
    
    Route::get('/suggestions', [\App\Http\Controllers\Admin\SuggestionController::class, 'index'])
        ->name('admin.suggestions.index');
    
    // API routes for suggestion rules CRUD
    Route::prefix('api/suggestions')->group(function () {
        Route::post('/rules', [\App\Http\Controllers\Admin\SuggestionController::class, 'store'])
            ->name('admin.suggestions.rules.store');
        Route::get('/rules/{id}', [\App\Http\Controllers\Admin\SuggestionController::class, 'show'])
            ->name('admin.suggestions.rules.show');
        Route::put('/rules/{id}', [\App\Http\Controllers\Admin\SuggestionController::class, 'update'])
            ->name('admin.suggestions.rules.update');
        Route::delete('/rules/{id}', [\App\Http\Controllers\Admin\SuggestionController::class, 'destroy'])
            ->name('admin.suggestions.rules.destroy');
    });
    
    // API route for getting all students
    Route::get('api/students', [\App\Http\Controllers\Admin\DashboardController::class, 'getAllStudents'])
        ->name('admin.students.api.index');

    // API route for getting chart data
    Route::get('api/charts', [\App\Http\Controllers\Admin\DashboardController::class, 'getChartData'])
        ->name('admin.charts.api.data');

    // Student Data & Backup Page
    Route::get('/students-backup', [\App\Http\Controllers\Admin\DashboardController::class, 'studentBackup'])
        ->name('admin.students.backup');
});

// Protected Teacher Routes - MANUAL CHECKS
Route::prefix('teacher')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('teacher.dashboard');
    
    // Approval Routes
    Route::prefix('approvals')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])
            ->name('teacher.approvals.index');
        
        Route::get('/{id}', [ApprovalController::class, 'show'])
            ->name('teacher.approvals.show');
        
        Route::post('/{id}/approve', [ApprovalController::class, 'approve'])
            ->name('teacher.approvals.approve');
        
        Route::post('/{id}/reject', [ApprovalController::class, 'reject'])
            ->name('teacher.approvals.reject');
            
        Route::post('/{id}/archive', [ApprovalController::class, 'archive'])
            ->name('teacher.approvals.archive');
            
        Route::post('/{id}/restore', [ApprovalController::class, 'restore'])
            ->name('teacher.approvals.restore');
    });
    
    // Student Management 
    Route::get('/students', function () {
        $user = auth()->user();
        if ($user->role !== 'teacher') {
            abort(403, 'Teacher access required. Your role: ' . $user->role);
        }
        return view('teacher.manage-students');
    })->name('teacher.students.index');
});

// Protected Student Routes - MANUAL CHECKS
Route::prefix('student')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role !== 'student') {
        abort(403, 'Student access required. Your role: ' . $user->role);
    }
    
    // Get student activities
    $activities = \App\Models\Activity::where('student_id', $user->student->id)
        ->orderBy('created_at', 'desc')
        ->get();
        
    return view('student.dashboard', compact('activities'));
})->name('student.dashboard');
    
    // History of all submissions
    Route::get('/history', [ActivityController::class, 'index'])
        ->name('student.activities.index');
    
    // Activity Routes
    Route::post('/activities', [ActivityController::class, 'store'])->name('student.activities.store');
    Route::get('/activities/{id}/edit', [ActivityController::class, 'edit'])->name('student.activities.edit');
    Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('student.activities.update');
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->name('student.activities.destroy');
    Route::post('/activities/{id}/appeal', [ActivityController::class, 'appeal'])->name('student.activities.appeal');
    
    // Document routes
    Route::delete('/documents/{id}', [ActivityController::class, 'destroyDocument'])
        ->name('student.documents.destroy');
    
    // Score calculation
    Route::get('/score', [ActivityController::class, 'calculateScore'])
        ->name('student.score');

    // Download PAJSK score report (PDF)
    Route::get('/report', [ActivityController::class, 'downloadReport'])
        ->name('student.report');
});

// Storage route for file access
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.local');

// Teacher student management API routes 
Route::middleware(['auth'])->group(function () {
    // Get students list
    Route::get('/teacher/students', [StudentController::class, 'index'])->name('teacher.students.index');
    
    // Export Class Report
    Route::get('/teacher/students/export-report', [StudentController::class, 'exportReport'])->name('teacher.students.export-report');
    
    // API routes  AJAX calls
    Route::prefix('api/teacher')->group(function () {
        Route::get('/students', [StudentController::class, 'apiIndex'])->name('teacher.students.api.index');
        Route::post('/students', [StudentController::class, 'store'])->name('teacher.students.api.store');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('teacher.students.api.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('teacher.students.api.destroy');
        Route::post('/students/{id}/marks', [StudentController::class, 'apiUpdateMarks'])->name('teacher.students.api.marks');
    });
    
    // Web routes non-AJAX
    Route::post('/students', [StudentController::class, 'store'])->name('teacher.students.store');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('teacher.students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('teacher.students.destroy');
});