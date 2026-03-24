@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 rounded-xl">
                    <i class="fas fa-user-graduate text-xl text-indigo-900"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $student->user->name }}</h1>
                    <p class="text-gray-600">IC: {{ $student->user->ic_number }} • Class: {{ ucfirst($student->teacher->assigned_class) }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('teacher.students.edit', $student->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route('teacher.students.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Student Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Personal Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <p class="font-medium">{{ $student->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">IC Number</p>
                    <p class="font-medium font-mono">{{ $student->user->ic_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Gender</p>
                    <p class="font-medium">{{ ucfirst($student->user->gender) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $student->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-medium">{{ $student->user->phone ?? 'Not set' }}</p>
                </div>
            </div>
        </div>

        <!-- Academic Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Academic Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Academic Session</p>
                    <p class="font-medium">{{ $student->academic_session }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Semester</p>
                    <p class="font-medium">Semester {{ $student->semester }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Class</p>
                    <p class="font-medium">{{ ucfirst($student->teacher->assigned_class) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Class Teacher</p>
                    <p class="font-medium">{{ $student->teacher->user->name }}</p>
                </div>
            </div>
        </div>

        <!-- Activities Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activities Participation</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Sports / Games</p>
                    <p class="font-medium">{{ $student->sports ? ucfirst(str_replace('_', ' ', $student->sports)) : 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Club / Association</p>
                    <p class="font-medium">{{ $student->club ? ucfirst(str_replace('_', ' ', $student->club)) : 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Uniform Unit</p>
                    <p class="font-medium">{{ $student->uniform ? ucfirst(str_replace('_', ' ', $student->uniform)) : 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Position</p>
                    <p class="font-medium">{{ $student->position ? ucfirst(str_replace('_', ' ', $student->position)) : 'None' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">PAJSK Marks</h3>
            <button onclick="openEditMarksModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <i class="fas fa-chart-bar"></i>
                Edit Marks
            </button>
        </div>
        
        @if($student->currentMark)
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Uniform Body</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $student->currentMark->uniform }}/20</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Club & Society</p>
                    <p class="text-2xl font-bold text-green-700">{{ $student->currentMark->club }}/20</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Sports & Games</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $student->currentMark->sport }}/20</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-sm text-gray-600">Competition</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $student->currentMark->competition }}/40</p>
                </div>
                <div class="text-center p-4 bg-pink-50 rounded-lg">
                    <p class="text-sm text-gray-600">Extra</p>
                    <p class="text-2xl font-bold text-pink-700">{{ $student->currentMark->extra }}</p>
                </div>
                <div class="text-center p-4 bg-indigo-50 rounded-lg">
                    <p class="text-sm text-gray-600">Total Grade</p>
                    <p class="text-2xl font-bold text-indigo-700">{{ $student->currentMark->total }}/100</p>
                    <p class="text-lg font-bold">{{ $student->currentMark->grade }}</p>
                </div>
            </div>
            
            @if($student->currentMark->is_manual_override)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">Manually updated by teacher</p>
                    @if($student->currentMark->override_reason)
                        <p class="text-sm">Reason: {{ $student->currentMark->override_reason }}</p>
                    @endif
                </div>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-bar text-3xl mb-3 opacity-50"></i>
                <p>No marks recorded yet</p>
            </div>
        @endif
    </div>

    <!-- Activities History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Submitted Activities</h3>
        
        @if($student->activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Activity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Level</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($student->activities as $activity)
                            <tr>
                                <td class="px-4 py-3">{{ $activity->name }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $typeLabels = [
                                            'uniform' => 'Uniform',
                                            'club' => 'Club',
                                            'sport' => 'Sport',
                                            'competition' => 'Competition',
                                            'extra' => 'Extra'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs bg-gray-100 rounded">{{ $typeLabels[$activity->type] ?? $activity->type }}</span>
                                </td>
                                <td class="px-4 py-3">{{ ucfirst($activity->level) }}</td>
                                <td class="px-4 py-3">
                                    @if($activity->status == 'pending')
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                    @elseif($activity->status == 'approved')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Approved</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $activity->activity_date->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-clipboard-list text-3xl mb-3 opacity-50"></i>
                <p>No activities submitted yet</p>
            </div>
        @endif
    </div>
</div>

<!-- Edit Marks Modal -->
<div id="editMarksModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Edit PAJSK Marks</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="{{ route('teacher.students.marks.update', $student->id) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uniform Body (0-20)</label>
                        <input type="number" name="uniform" value="{{ $student->currentMark->uniform ?? 0 }}" min="0" max="20" 
                               class="w-full px-3 py-2