<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Display the student profile.
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        
        return view('student.profile', compact('user', 'student'));
    }
}