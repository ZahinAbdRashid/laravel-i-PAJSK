<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    // Show login form based on role
    public function showLoginForm($role)
    {
        if (!in_array($role, ['admin', 'teacher', 'student'])) {
            abort(404);
        }
        
        return view("auth.login-{$role}");
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'ic_number' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:admin,teacher,student'
        ]);

        // Check if user exists with this IC and role
        $user = User::where('ic_number', $request->ic_number)
                    ->where('role', $request->role)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'ic_number' => 'Invalid IC or Password.'
            ])->withInput();
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Invalid IC or Password.'
            ])->withInput();
        }

        // Login the user
        Auth::login($user);

        // Redirect based on role
        return $this->redirectBasedOnRole($user);
    }

    // Redirect user based on their role
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect('/');
        }
    }

    // Logout
    public function logout(Request $request)
    {
        // Get user role before logout
        $user = Auth::user();
        $role = $user ? $user->role : null;
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect to appropriate login page based on role
        if ($role && in_array($role, ['admin', 'teacher', 'student'])) {
            return redirect()->route('login.form', ['role' => $role]);
        }
        
        // Default to student login if role is unknown
        return redirect()->route('login.form', ['role' => 'admin']);
    }
}