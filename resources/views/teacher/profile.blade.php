@extends('layouts.app')

@section('title', 'Teacher Profile')

@push('styles')
<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #162660 0%, #2d3a7c 100%); }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $teacher = $user->teacher;
@endphp

<!-- Profile Header -->
<div class="bg-gradient-primary rounded-t-xl sm:rounded-xl p-6 sm:p-8 text-white mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold mb-2">Teacher Profile</h2>
            <p class="opacity-90">SMK Dato' Haji Talib Karim</p>
        </div>
        <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center">
            <i class="fas fa-chalkboard-teacher text-3xl"></i>
        </div>
    </div>
</div>

<!-- Cards Container -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
    <!-- Personal Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-xl font-semibold text-indigo-900 flex items-center gap-3">
                <i class="fas fa-id-card"></i>
                Personal Information
            </h3>
        </div>
        <div class="p-6">
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
                
                <!-- Staff ID -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Staff ID</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $teacher->staff_id ?? '-' }}</p>
                </div>
                
                <!-- Subject -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $teacher->subject ?? '-' }}</p>
                </div>
                
                <!-- Class -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Class</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">
                        {{ ucfirst($teacher->assigned_class) ?? 'Not assigned' }}
                    </p>
                </div>
                
                <!-- Email -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $user->email }}</p>
                </div>
                
                <!-- Phone -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</p>
                    <p class="text-gray-800 font-medium py-2 border-b border-gray-100">{{ $user->phone ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-xl font-semibold text-indigo-900 flex items-center gap-3">
                <i class="fas fa-key"></i>
                Change Password
            </h3>
        </div>
        <div class="p-6">
            <form id="changePasswordForm" class="space-y-6" method="POST" action="{{ route('profile.password') }}">
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            placeholder="Enter current password"
                            required
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-900 transition"></i>
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            placeholder="Enter new password"
                            required
                            minlength="6"
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-900 transition"></i>
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
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            placeholder="Re-enter new password"
                            required
                        >
                        <i class="fas fa-eye toggle-password absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-indigo-900 transition"></i>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-indigo-900 text-white rounded-lg font-semibold hover:bg-indigo-800 transition"
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