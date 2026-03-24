@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-indigo-50 rounded-xl">
                <i class="fas fa-edit text-xl text-indigo-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Student</h1>
                <p class="text-gray-600">{{ $student->user->name }} • IC: {{ $student->user->ic_number }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Update Student Information</h3>
        </div>
        
        <div class="p-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('teacher.students.update', $student->id) }}">
                @csrf
                @method('PUT')
                @include('teacher.students.form')
            </form>
        </div>
    </div>
</div>
@endsection