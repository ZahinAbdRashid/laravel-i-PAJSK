@extends('layouts.app')

@section('title', 'Student Data & Backup')

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Student Data & Backup</h1>
                <p class="text-gray-700 mb-3">View and backup all student records</p>
            </div>
            <div class="flex gap-3">
                <button onclick="generateReport()" class="px-5 py-2.5 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition flex items-center gap-2">
                    <i class="fas fa-file-export"></i>
                    Backup Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Student List Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="border-b border-gray-100 px-6 py-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-indigo-900">All Students List</h3>
            <div class="text-sm text-gray-600">
                <span id="studentCount">0</span> students found
            </div>
        </div>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full min-w-[1300px]">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs font-medium text-gray-700">
                        <th class="px-3 py-3">No.</th>
                        <th class="px-3 py-3">Student Name</th>
                        <th class="px-3 py-3">IC Number</th>
                        <th class="px-3 py-3">Class</th>
                        <th class="px-3 py-3">Academic Session</th>
                        <th class="px-3 py-3">Semester</th>
                        <th class="px-3 py-3">Sports / Games</th>
                        <th class="px-3 py-3">Club / Association</th>
                        <th class="px-3 py-3">Uniform Unit</th>
                        <th class="px-3 py-3">Position</th>
                        <th class="px-3 py-3">Total Marks</th>
                    </tr>
                </thead>
                <tbody id="studentList" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="11" class="px-3 py-8 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                            <p class="text-sm">Loading student data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Students data
    let students = [];
    
    // Initialize page
    function initializePage() {
        loadStudentsData();
    }
    
    // Load students data
    function loadStudentsData() {
        fetch('/admin/api/students', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.students) {
                students = data.students;
                renderStudentList();
            } else {
                console.error('Failed to load students:', data);
                renderStudentList();
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            renderStudentList();
        });
    }
    
    // Render student list
    function renderStudentList() {
        const container = document.getElementById('studentList');
        
        if (students.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="11" class="px-3 py-12 text-center text-gray-500">
                        <i class="fas fa-user-graduate text-3xl mb-4 opacity-50"></i>
                        <p class="mb-2">No students found</p>
                        <p class="text-xs">Student data will appear here once available</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        students.forEach((student, index) => {
            html += `
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-3 text-gray-500 text-xs">${index + 1}</td>
                    <td class="px-3 py-3">
                        <div class="font-medium text-gray-900 text-xs">${student.name || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs font-mono">${student.ic || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.class || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.academicSession || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.semester || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.sports || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.club || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.uniform || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs">${student.position || '-'}</div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-gray-700 text-xs font-semibold">${student.totalMarks || '0'}</div>
                    </td>
                </tr>
            `;
        });
        
        container.innerHTML = html;
        document.getElementById('studentCount').textContent = students.length;
    }
    
    // Generate report function
    function generateReport() {
        if (students.length === 0) {
            showToast('No student data to generate report!', 'error');
            return;
        }
        
        let csvContent = "No,Student Name,IC Number,Class,Academic Session,Semester,Gender,Sports/Games,Club/Association,Uniform Unit,Position,Total Marks\n";            
        students.forEach((student, index) => {
            csvContent += `${index + 1},"${student.name || ''}","${student.ic || ''}","${student.class || ''}","${student.academicSession || ''}","${student.semester || ''}","${student.gender || ''}","${student.sports || ''}","${student.club || ''}","${student.uniform || ''}","${student.position || ''}",${student.totalMarks || 0}\n`;
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        
        const now = new Date();
        const dateStr = now.toISOString().split('T')[0];
        const filename = `PAJSK_Student_Report_${dateStr}.csv`;
        
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast(`Report generated successfully!`);
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initializePage);
</script>
@endpush
