<?php

// routes/api.php
Route::prefix('api')->group(function () {
    
    // AUTHENTICATION
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    
    // STUDENT MANAGEMENT (Teacher Only)
    Route::middleware(['auth:teacher'])->group(function () {
        Route::apiResource('students', StudentController::class);
        Route::post('students/{id}/marks', [StudentController::class, 'updateMarks']);
    });
    
    // STUDENT ACTIVITIES (Student & Teacher)
    Route::apiResource('activities', ActivityController::class);
    Route::post('activities/{id}/approve', [ActivityController::class, 'approve']);
    Route::post('activities/{id}/reject', [ActivityController::class, 'reject']);
    
    // ADMIN ROUTES (Admin Only)
    Route::middleware(['auth:admin'])->group(function () {
        Route::apiResource('suggestion-rules', SuggestionRuleController::class);
        Route::get('admin/students', [\App\Http\Controllers\Admin\DashboardController::class, 'getAllStudents']);
    });
    
    // REPORTS (Admin Only)
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('reports/students', [ReportController::class, 'students']);
        Route::get('reports/export', [ReportController::class, 'export']);
    });
});