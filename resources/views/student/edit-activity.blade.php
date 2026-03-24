@extends('layouts.app')

@section('title', 'Edit Activity')

@push('styles')
<style>
    .upload-area { transition: all 0.3s ease; }
    .upload-area.dragover { border-color: #162660; background-color: #f8fafc; }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $student = $user->student;
@endphp

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Activity</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Update your activity details</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="border-b border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
            <h3 class="text-base sm:text-lg font-semibold text-indigo-900 flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span class="break-words">Edit Activity: {{ $activity->name }}</span>
            </h3>
        </div>
        
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
            <form id="editActivityForm" method="POST" action="{{ route('student.activities.update', $activity->id) }}" 
                  enctype="multipart/form-data">
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
                
                <!-- Activity Type (Disabled for edit) -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Activity Type</label>
                    <input type="text" value="{{ ucfirst($activity->type) }}" 
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg bg-gray-50" 
                           disabled>
                    <input type="hidden" name="type" value="{{ $activity->type }}">
                </div>

                <!-- Activity Name -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Activity Name *</label>
                    <input type="text" name="name" id="activityName" 
                           value="{{ $activity->name }}" required
                           class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                           placeholder="Enter Activity Name">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <!-- Level -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Level *</label>
                        <select name="level" id="activityLevel" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition bg-white">
                            <option value="school" {{ $activity->level == 'school' ? 'selected' : '' }}>School</option>
                            <option value="district" {{ $activity->level == 'district' ? 'selected' : '' }}>District</option>
                            <option value="state" {{ $activity->level == 'state' ? 'selected' : '' }}>State</option>
                            <option value="national" {{ $activity->level == 'national' ? 'selected' : '' }}>National</option>
                            <option value="international" {{ $activity->level == 'international' ? 'selected' : '' }}>International</option>
                        </select>
                    </div>

                    <!-- Achievement -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Achievement *</label>
                        <select name="achievement" id="achievement" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 outline-none bg-white">
                            <option value="participation" {{ $activity->achievement == 'participation' ? 'selected' : '' }}>Participation</option>
                            <option value="third" {{ $activity->achievement == 'third' ? 'selected' : '' }}>Third Place</option>
                            <option value="second" {{ $activity->achievement == 'second' ? 'selected' : '' }}>Runner-Up</option>
                            <option value="first" {{ $activity->achievement == 'first' ? 'selected' : '' }}>Champion</option>
                        </select>
                    </div>
                </div>
                
                <!-- Activity Date -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Activity Date *</label>
                    <input type="date" name="activity_date" id="activityDate" required
                        value="{{ $activity->activity_date->format('Y-m-d') }}"
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition">
                </div>
           
                <!-- Existing Documents -->
                @if($documents->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    <h4 class="text-sm sm:text-md font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fas fa-paperclip"></i>
                        Current Documents
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                        @foreach($documents as $document)
                        <div class="flex items-center justify-between bg-gray-50 p-2.5 sm:p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                @if($document->mime_type == 'application/pdf')
                                <i class="fas fa-file-pdf text-base sm:text-lg text-red-500 flex-shrink-0"></i>
                                @else
                                <i class="fas fa-file-image text-base sm:text-lg text-green-500 flex-shrink-0"></i>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-xs sm:text-sm text-gray-800 truncate">
                                        {{ $document->original_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ round($document->size / 1024, 1) }} KB
                                    </div>
                                </div>
                            </div>
                            <button type="button" 
                                    onclick="deleteDocument({{ $document->id }})"
                                    class="text-gray-400 hover:text-red-500 transition flex-shrink-0 ml-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Add New Documents -->
                <div class="space-y-3 sm:space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <h4 class="text-sm sm:text-md font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i>
                            Add More Documents (Optional)
                        </h4>
                        <span class="text-xs text-gray-500">Max: 5MB each</span>
                    </div>
                    
                    <div id="uploadArea" class="upload-area border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center cursor-pointer hover:border-indigo-900 hover:bg-blue-50 transition">
                        <i class="fas fa-cloud-upload-alt text-2xl sm:text-3xl text-indigo-900 mb-2 sm:mb-3"></i>
                        <p class="text-sm sm:text-base font-medium text-gray-700 mb-1">Click or Drag Files Here</p>
                        <p class="text-xs sm:text-sm text-gray-500">PDF, JPG, PNG formats</p>
                        <input type="file" name="documents[]" id="fileInput" class="hidden" 
                               accept=".pdf,.jpg,.jpeg,.png" multiple>
                    </div>
                    
                    <div id="filePreviewContainer" class="space-y-2 max-h-40 overflow-y-auto scrollbar-thin"></div>
                </div>

                <!-- Buttons Form Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 sm:pt-6 border-t border-gray-200">
                    <a href="{{ route('student.activities.index') }}" 
                       class="flex-1 py-2.5 sm:py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition text-center text-sm sm:text-base">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex-1 py-2.5 sm:py-3 bg-indigo-900 text-white font-medium rounded-lg hover:bg-indigo-800 transition text-sm sm:text-base">
                        Update Submission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // File Upload Handlers
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    
    let uploadedFiles = [];

    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                showToast(`File ${file.name} is too large. Max 5MB.`, 'error');
                return;
            }
            
            const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                showToast(`File format ${file.name} is not supported.`, 'error');
                return;
            }
            
            uploadedFiles.push(file);
            displayFilePreview(file);
        });
    }

    function displayFilePreview(file) {
        const preview = document.createElement('div');
        preview.className = 'flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200';
        
        const fileIcon = file.type === 'application/pdf' ? 'fa-file-pdf text-red-500' : 
                        file.type.startsWith('image/') ? 'fa-file-image text-green-500' : 
                        'fa-file text-gray-500';
        
        preview.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas ${fileIcon} text-lg"></i>
                <div>
                    <div class="font-medium text-sm text-gray-800 truncate max-w-xs">${file.name}</div>
                    <div class="text-xs text-gray-500">${formatFileSize(file.size)}</div>
                </div>
            </div>
            <button type="button" onclick="removeFile('${file.name}')" class="text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        filePreviewContainer.appendChild(preview);
    }

    function removeFile(fileName) {
        uploadedFiles = uploadedFiles.filter(f => f.name !== fileName);
        const previews = filePreviewContainer.querySelectorAll('.flex.bg-gray-50');
        Array.from(previews).forEach(preview => {
            const nameElement = preview.querySelector('.font-medium');
            if (nameElement && nameElement.textContent === fileName) {
                preview.remove();
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // Delete document via AJAX
    function deleteDocument(documentId) {
        if (confirm('Are you sure you want to delete this document?')) {
            fetch(`/student/documents/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Document deleted successfully', 'success');
                    // Reload page to reflect changes
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                showToast('Error deleting document', 'error');
            });
        }
    }
</script>
@endpush