@extends('layouts.app')

@section('title', 'Teacher Details')

@section('content')
    <div class="max-w-6xl mx-auto"> <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Teacher Details</h1>
                    <p class="text-gray-600">View teacher information and assigned students</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.teachers.index') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-semibold text-indigo-900">Teacher Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</p>
                                    <p class="text-gray-800 font-medium mt-1">{{ $teacher->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">IC Number</p>
                                    <p class="text-gray-800 font-medium mt-1">{{ $teacher->user->ic_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</p>
                                    <p class="text-gray-800 font-medium mt-1">{{ $teacher->user->email }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Staff ID</p>
                                    <p class="text-gray-800 font-medium mt-1 font-mono">{{ $teacher->staff_id }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</p>
                                    <p class="text-gray-800 font-medium mt-1">{{ $teacher->subject }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Class</p>
                                    <span class="mt-1 inline-block text-sm font-semibold text-gray-800 tracking-wide">
                                        {{ strtoupper($teacher->assigned_class) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-700">Assigned Students</h3>
                        <span class="px-3 py-1  text-gray-700 rounded-full text-xs font-bold">
                            {{ $teacher->students->count() }} Students
                        </span>
                    </div>
                    <div class="p-0"> @if($teacher->students->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                                        <tr>
                                            <th class="px-6 py-4">No</th>
                                            <th class="px-6 py-4">Student Name</th>
                                            <th class="px-6 py-4">IC Number</th>
                                            <th class="px-6 py-4 text-center">Academic Session</th>
                                            <th class="px-6 py-4 text-center">Semester</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($teacher->students as $index => $student)
                                            <tr class="hover:bg-blue-50/30 transition">
                                                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-6 py-4 font-medium text-gray-900">{{ $student->user->name }}</td>
                                                <td class="px-6 py-4 text-gray-600">{{ $student->user->ic_number }}</td>
                                                <td class="px-6 py-4 text-center text-gray-600">{{ $student->academic_session }}</td>
                                                <td class="px-6 py-4 text-center">
                                                    <span class="px-2 py-1 text-gray-700">Sem {{ $student->semester }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                <i class="fas fa-user-graduate text-4xl mb-3 opacity-20"></i>
                                <p>No students assigned to this teacher yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-semibold text-indigo-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <a href="{{ route('admin.teachers.edit', $teacher) }}"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                <i class="fas fa-edit"></i>
                                Edit Teacher
                            </a>

                            <hr class="my-4 border-gray-100">

                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this teacher? All assigned students will need to be reassigned.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition border border-red-100">
                                    <i class="fas fa-trash-alt"></i>
                                    Delete Teacher
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection