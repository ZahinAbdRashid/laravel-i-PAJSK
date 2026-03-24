@php
    $isEdit = isset($student);
    $user = $isEdit ? $student->user : null;
@endphp

<div class="space-y-6">
    <!-- Personal Information -->
    <div class="space-y-4">
        <h4 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Personal Information</h4>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900"
                   placeholder="Enter student's full name">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IC Number *</label>
                <input type="text" name="ic_number" value="{{ old('ic_number', $user->ic_number ?? '') }}" 
                       {{ $isEdit ? 'readonly' : 'required' }}
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 {{ $isEdit ? 'bg-gray-50' : '' }}"
                       placeholder="e.g., 010203-14-5678">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                <select name="gender" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Session *</label>
                <select name="academic_session" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    <option value="">Select Academic Session</option>
                    @foreach($academicSessions as $session)
                        <option value="{{ $session }}" {{ old('academic_session', $student->academic_session ?? '') == $session ? 'selected' : '' }}>
                            {{ $session }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                <select name="semester" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    <option value="">Select Semester</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}" {{ old('semester', $student->semester ?? '') == $semester ? 'selected' : '' }}>
                            Semester {{ $semester }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Activities Information -->
    <div class="space-y-4">
        <h4 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Activities Information</h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sports / Games</label>
                <select name="sports" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    @foreach($sportsOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('sports', $student->sports ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Club / Association</label>
                <select name="club" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    @foreach($clubOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('club', $student->club ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Uniform Unit</label>
                <select name="uniform" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    @foreach($uniformOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('uniform', $student->uniform ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                <select name="position" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                    @foreach($positionOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('position', $student->position ?? '') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex gap-3 pt-6 border-t border-gray-200">
        <a href="{{ route('teacher.students.index') }}" class="flex-1 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition text-center">
            Cancel
        </a>
        <button type="submit" class="flex-1 py-3 bg-indigo-900 text-white font-medium rounded-lg hover:bg-indigo-800 transition">
            {{ $isEdit ? 'Update Student' : 'Save Student' }}
        </button>
    </div>
</div>