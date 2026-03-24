@extends('layouts.app')

@section('title', 'Edit Teacher')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Edit Teacher</h1>
        <p class="text-gray-600">Update teacher information</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-semibold text-indigo-900">Edit Teacher Information</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="p-6">
            @csrf
            @method('PUT')
            
            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <div class="space-y-6">
                <!-- Personal Information -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Personal Information</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" name="name" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none"
                                   value="{{ old('name', $teacher->user->name) }}"
                                   placeholder="Enter full name">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">IC Number *</label>
                            <input type="text" name="ic_number" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none"
                                   value="{{ old('ic_number', $teacher->user->ic_number) }}"
                                   placeholder="e.g., 901212-14-5678">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Email Address *</label>
                            <input type="email" name="email" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none"
                                   value="{{ old('email', $teacher->user->email) }}"
                                   placeholder="teacher@school.edu.my">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Phone Number *</label>
                            <input type="tel" name="phone" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none"
                                   value="{{ old('phone', $teacher->user->phone) }}"
                                   placeholder="e.g., 012-3456789">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Gender *</label>
                            <select name="gender" required 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $teacher->user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $teacher->user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Professional Information -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Professional Information</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Staff ID *</label>
                            <input type="text" name="staff_id" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none"
                                   value="{{ old('staff_id', $teacher->staff_id) }}"
                                   placeholder="e.g., STF2024001">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Subject *</label>
                            <input type="text" name="subject" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none"
                                   value="{{ old('subject', $teacher->subject) }}"
                                   placeholder="e.g., Mathematics, Science, etc.">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Class Assign *</label>
                            <select name="assigned_class" required 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                                <option value="">Select Class</option>
                                <option value="alpha" {{ old('assigned_class', $teacher->assigned_class) == 'alpha' ? 'selected' : '' }}>Alpha</option>
                                <option value="delta" {{ old('assigned_class', $teacher->assigned_class) == 'delta' ? 'selected' : '' }}>Delta</option>
                                <option value="omega" {{ old('assigned_class', $teacher->assigned_class) == 'omega' ? 'selected' : '' }}>Omega</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Note -->
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        To change password, ask the teacher to use "Change Password" in their profile.
                    </p>
                
                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.teachers.index') }}" 
                       class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition">
                        Update Teacher
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection