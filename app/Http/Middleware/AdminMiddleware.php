<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login.form', ['role' => 'admin']);
        }

        // Check if user is admin
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}