@extends('layouts.app')

@section('title', 'Manage Teachers')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Manage Teachers</h1>
            <p class="text-gray-600">Add, edit, or remove teacher accounts</p>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="px-5 py-2.5 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            Add New Teacher
        </a>
    </div>
</div>

<!-- Teachers List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="border-b border-gray-100 px-6 py-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-indigo-900">Teacher List</h3>
            <div class="text-sm text-gray-600">
                {{ $teachers->count() }} teachers found
            </div>
        </div>
    </div>
    <div class="p-6">
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif
        
        @if(session('confirm_delete'))
        @php
            $confirmData = session('confirm_delete');
        @endphp
        <div class="mb-4 bg-yellow-50 border border-yellow-300 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-yellow-900 mb-2">Confirm Delete Teacher</h4>
                    <p class="text-yellow-800 mb-3">{{ $confirmData['message'] }}</p>
                    <div class="flex gap-2">
                        <form action="{{ route('admin.teachers.destroy', $confirmData['teacher_id']) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="confirm_delete" value="1">
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                                <i class="fas fa-trash-alt mr-2"></i>Yes, Delete Teacher & Students
                            </button>
                        </form>
                        <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm font-medium text-gray-700">
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Staff ID</th>
                        <th class="px-6 py-3">IC Number</th>
                        <th class="px-6 py-3">Subject</th>
                        <th class="px-6 py-3">Class</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody id="teacherList" class="divide-y divide-gray-100">
                    @forelse($teachers as $teacher)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $teacher->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $teacher->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 font-mono">{{ $teacher->staff_id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700">{{ $teacher->user->ic_number }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700">{{ $teacher->subject }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700">
                                {{ ucfirst($teacher->assigned_class) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                   class="text-gray-600 hover:text-gray-800 p-2 hover:bg-gray-50 rounded transition" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-3xl mb-4 opacity-50"></i>
                            <p class="mb-2">No teachers found</p>
                            <p class="text-sm">Click "Add New Teacher" to add a teacher</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($teachers->hasPages())
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Showing {{ $teachers->firstItem() }} to {{ $teachers->lastItem() }} of {{ $teachers->total() }} results
            </div>
            <div class="flex gap-2">
                @if($teachers->onFirstPage())
                <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded cursor-not-allowed">Previous</span>
                @else
                <a href="{{ $teachers->previousPageUrl() }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Previous</a>
                @endif
                
                @if($teachers->hasMorePages())
                <a href="{{ $teachers->nextPageUrl() }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Next</a>
                @else
                <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toast notification function for success messages
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        
        // Set toast styles based on type
        const styles = {
            success: 'bg-green-50 text-green-800 border-green-200',
            error: 'bg-red-50 text-red-800 border-red-200',
            info: 'bg-blue-50 text-blue-800 border-blue-200',
            warning: 'bg-yellow-50 text-yellow-800 border-yellow-200'
        };
        
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle',
            warning: 'fa-exclamation-triangle'
        };
        
        toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg border shadow-lg ${styles[type]} transition-all duration-300 transform translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <i class="fas ${icons[type]}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
    
    // Show success message if there is a success session
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('success') }}', 'success');
        });
    @endif
    
    // Confirm delete with custom alert
    document.addEventListener('DOMContentLoaded', function() {
        // Attach event listeners to delete forms
        const deleteForms = document.querySelectorAll('.delete-teacher-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const teacherName = this.dataset.teacherName || 'this teacher';
                const studentsCount = parseInt(this.dataset.studentsCount) || 0;
                const formToSubmit = this; // Store form reference
                
                // Custom confirmation modal
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4';
                modal.id = 'deleteTeacherModal';
                
                let message = `Are you sure you want to delete <strong>${teacherName}</strong>?`;
                if (studentsCount > 0) {
                    message += `<br><br><span class="text-red-600 font-semibold">Warning:</span> This teacher has <strong>${studentsCount} student(s)</strong>. Deleting this teacher will also delete all students and their data (activities, marks, etc.). This action cannot be undone.`;
                } else {
                    message += ' This action cannot be undone.';
                }
                
                modal.innerHTML = `
                    <div class="bg-white rounded-xl w-full max-w-md">
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <div class="mx-auto w-12 h-12 rounded-full ${studentsCount > 0 ? 'bg-red-100' : 'bg-yellow-100'} flex items-center justify-center mb-4">
                                    <i class="fas fa-exclamation-triangle ${studentsCount > 0 ? 'text-red-600' : 'text-yellow-600'} text-xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Teacher</h3>
                                <p class="text-gray-600">${message}</p>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" id="cancelDeleteBtn" 
                                        class="flex-1 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition">
                                    Cancel
                                </button>
                                <button type="button" id="confirmDeleteBtn" 
                                        class="flex-1 py-3 ${studentsCount > 0 ? 'bg-red-600' : 'bg-orange-600'} text-white font-medium rounded-lg ${studentsCount > 0 ? 'hover:bg-red-700' : 'hover:bg-orange-700'} transition">
                                    ${studentsCount > 0 ? 'Delete Teacher & Students' : 'Delete Teacher'}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                
                // Add event listeners to buttons
                const cancelBtn = modal.querySelector('#cancelDeleteBtn');
                const confirmBtn = modal.querySelector('#confirmDeleteBtn');
                
                cancelBtn.addEventListener('click', function() {
                    modal.remove();
                });
                
                confirmBtn.addEventListener('click', function() {
                    // Remove modal first
                    modal.remove();
                    // Submit the form
                    formToSubmit.submit();
                });
                
                return false;
            });
        });
    });
    
    // Search/filter functionality
    function filterTeachers() {
        const searchInput = document.getElementById('teacherSearch');
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#teacherList tr');
            
            rows.forEach(row => {
                if (row.querySelector('td')) {
                    const name = row.querySelector('td:first-child .font-medium')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('td:first-child .text-xs')?.textContent.toLowerCase() || '';
                    const staffId = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const ic = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                    
                    const match = name.includes(searchTerm) || 
                                  email.includes(searchTerm) || 
                                  staffId.includes(searchTerm) || 
                                  ic.includes(searchTerm);
                    
                    row.style.display = match ? '' : 'none';
                }
            });
            
            // Update count
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none' && row.querySelector('td'));
            const countElement = document.querySelector('.text-sm.text-gray-600');
            if (countElement) {
                countElement.textContent = visibleRows.length + ' teachers found';
            }
        });
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        filterTeachers();
    });
</script>
@endpush