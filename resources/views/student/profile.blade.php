@extends('layouts.app')

@section('title', 'Student Profile')

@push('styles')
<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #162660 0%, #2d3a7c 100%); }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $student = $user->student;
@endphp

<!-- Profile Header -->
<div class="bg-gradient-primary rounded-t-xl sm:rounded-xl p-4 sm:p-6 md:p-8 text-white mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-6">
        <div>
            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold mb-2">Student Profile</h2>
        </div>
    </div>
</div>

<!-- Cards Container -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
    <!-- Personal Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
            <h3 class="text-lg sm:text-xl font-semibold text-indigo-900 flex items-center gap-2 sm:gap-3">
                <i class="fas fa-id-card"></i>
                Personal Information
            </h3>
        </div>
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Full Name -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $user->name }}</p>
                </div>
                
                <!-- IC Number -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">IC Number</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $user->ic_number }}</p>
                </div>
                
                <!-- Gender -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ ucfirst($user->gender) }}</p>
                </div>

                <!-- Class -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Class</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">
                        {{ ucfirst($student->teacher->assigned_class) ?? '-' }}
                    </p>
                </div>
                
                <!-- Semester -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $student->semester }}</p>
                </div>
                
                <!-- Academic Session -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Session</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $student->academic_session }}</p>
                </div>

                <!-- SPORTS / GAMES -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">SPORTS / GAMES</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $student->sports ?? 'None' }}</p>
                </div>

                <!-- CLUB / ASSOCIATION -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">CLUB / ASSOCIATION</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $student->club ?? 'None' }}</p>
                </div>

                <!-- UNIT UNIFORM -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">UNIT UNIFORM</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $student->uniform ?? 'None' }}</p>
                </div>

                <!-- POSITION -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">POSITION</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $student->position ?? 'None' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
            <h3 class="text-lg sm:text-xl font-semibold text-indigo-900 flex items-center gap-2 sm:gap-3">
                <i class="fas fa-key"></i>
                Change Password
            </h3>
        </div>
        <div class="p-4 sm:p-6">
            <form id="changePasswordForm" method="POST" action="{{ route('profile.password') }}" class="space-y-4 sm:space-y-6">
                @csrf
                @method('PUT')
                
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
                @endif
                
                @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Current Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-lock text-indigo-900"></i>
                        Current Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="current_password"
                            id="currentPassword"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            placeholder="Enter current password"
                            required
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-900 transition cursor-pointer"></i>
                    </div>
                </div>

                <!-- New Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-lock text-indigo-900"></i>
                        New Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="new_password"
                            id="newPassword"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            placeholder="Enter new password (Min. 6 characters)"
                            required
                            minlength="6"
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-900 transition cursor-pointer"></i>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-lock text-indigo-900"></i>
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="new_password_confirmation"
                            id="confirmPassword"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            placeholder="Re-enter new password"
                            required
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-900 transition cursor-pointer"></i>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 sm:py-3 bg-indigo-900 text-white rounded-lg font-semibold hover:bg-indigo-800 transition text-sm sm:text-base"
                    >
                        <i class="fas fa-save"></i>
                        Save Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection