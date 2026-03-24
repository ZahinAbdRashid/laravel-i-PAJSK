@extends('layouts.app')

@section('title', 'Manage Students')

@section('content')
<!-- Header Section -->
<div class="mb-10">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-indigo-50 rounded-xl">
                <i class="fas fa-users text-xl text-indigo-900"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Students</h1>
                <p id="teacherClassInfo" class="text-gray-600">
                    Class: <span class="font-semibold text-indigo-900">{{ Auth::user()->teacher->assigned_class ?? 'Not assigned' }}</span>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.students.export-report') }}" target="_blank" class="flex items-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-lg hover:bg-emerald-700 transition font-medium shadow-sm border border-emerald-700">
                <i class="fas fa-file-pdf"></i>
                <span>Download Class Report</span>
            </a>
            <button onclick="openAddStudentModal()" class="flex items-center gap-2 bg-indigo-900 text-white px-5 py-3 rounded-lg hover:bg-indigo-800 transition font-medium">
                <i class="fas fa-user-plus"></i>
                <span>Add New Student</span>
            </button>
        </div>
    </div>
</div>

<!-- Students List -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Student List</h3>
            <div class="text-sm text-gray-600" id="studentCount">Loading students...</div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Student Name</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">IC Number</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Sports / Games</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Club / Association</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Uniform Unit</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody" class="divide-y divide-gray-200">
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-xl mb-3"></i>
                        <p>Loading students...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Section --}}
<!-- Add/Edit Student Modal -->
<div id="studentModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add New Student</h3>
            <button onclick="closeModal('studentModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="studentForm" class="space-y-6">
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Personal Information</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" id="studentName" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900"
                               placeholder="Enter student's full name">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IC Number *</label>
                            <input type="text" id="icNumber" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900"
                                   placeholder="e.g., 010203-14-5678">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select id="studentGender" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Academic Session and Semester -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Session *</label>
                            <select id="academicSession" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Academic Session</option>
                                <option value="2023/2024">2023/2024</option>
                                <option value="2024/2025" selected>2024/2025</option>
                                <option value="2025/2026">2025/2026</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester *</label>
                            <select id="semester" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Semester</option>
                                <option value="1" selected>Semester 1</option>
                                <option value="2">Semester 2</option>
                                <option value="3">Semester 3</option>
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
                            <select id="sportsGames" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Sports/Games</option>
                                <option value="bola jaring">Bola Jaring</option>
                                <option value="badminton">Badminton</option>
                                <option value="basketball">Basketball</option>
                                <option value="football">Football</option>
                                <option value="athletics">Athletics</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Club / Association</label>
                            <select id="clubAssociation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Club/Association</option>
                                <option value="alam sekitar">Alam Sekitar</option>
                                <option value="ict">ICT Club</option>
                                <option value="english">English Language Society</option>
                                <option value="science">Science Club</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uniform Unit</label>
                            <select id="uniformUnit" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Uniform Unit</option>
                                <option value="pengakap">Pengakap</option>
                                <option value="kadet polis">Kadet Polis</option>
                                <option value="kadet bomba">Kadet Bomba</option>
                                <option value="puteri islam">Puteri Islam</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <select id="position" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 bg-white">
                                <option value="">Select Position</option>
                                <option value="pengawas sekolah">Pengawas Sekolah</option>
                                <option value="ketua kelas">Ketua Kelas</option>
                                <option value="penolong ketua kelas">Penolong Ketua Kelas</option>
                                <option value="ketua persatuan">Ketua Persatuan</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('studentModal')" class="flex-1 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-indigo-900 text-white font-medium rounded-lg hover:bg-indigo-800 transition">
                        Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Marks Modal -->
<div id="marksModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900" id="marksModalTitle">Edit PAJSK Marks</h3>
            <button onclick="closeModal('marksModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="marksStudentInfo" class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading student information...</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Uniform Body</label>
                        <input type="number" id="quickUniformMarks" min="0" max="20" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Club & Society</label>
                        <input type="number" id="quickClubMarks" min="0" max="20" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sports & Games</label>
                        <input type="number" id="quickSportMarks" min="0" max="20" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Competition</label>
                        <input type="number" id="quickCompetitionMarks" min="0" max="40" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900">
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-700 mb-2">Total Score</div>
                    <div id="quickTotalScore" class="text-2xl font-bold text-indigo-900">0/100</div>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button onclick="closeModal('marksModal')" class="flex-1 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button onclick="saveQuickMarks()" class="flex-1 py-3 bg-indigo-900 text-white font-medium rounded-lg hover:bg-indigo-800 transition">
                        Save Marks
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Students data - will be populated via AJAX
    let students = [];
    let editingStudentId = null;
    let quickEditingStudentId = null;

    // Initialize page
    function initializePage() {
        loadStudents();
        setupFormValidation();
        setupMarksCalculation();
    }
    
    // Load students from server
    function loadStudents() {
        fetch('/api/teacher/students', {
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
                students = data.students.map(student => {
                    return {
                        id: student.id,
                        name: student.name || 'N/A',
                        icNumber: student.icNumber || 'N/A',
                        gender: student.gender || 'N/A',
                        academicSession: student.academicSession || '',
                        semester: student.semester || '',
                        sportsGames: student.sportsGames || '',
                        clubAssociation: student.clubAssociation || '',
                        uniformUnit: student.uniformUnit || '',
                        position: student.position || '',
                        marks: student.marks || { uniform: 0, club: 0, sport: 0, competition: 0, extra: 0, total: 0, grade: 'E' }
                    };
                });
                renderStudentsTable();
            } else {
                console.error('Failed to load students:', data);
                showToast(data.message || 'Failed to load students', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            showToast('Error loading students', 'error');
        });
    }
    
    // Render students table
    function renderStudentsTable() {
        const tbody = document.getElementById('studentsTableBody');
        
        if (!tbody) {
            console.error('studentsTableBody element not found!');
            return;
        }
        
        if (students.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-3xl mb-4 opacity-50"></i>
                        <p class="mb-2">No students found</p>
                        <p class="text-sm">Click "Add New Student" to add students</p>
                    </td>
                </tr>
            `;
            const countElement = document.getElementById('studentCount');
            if (countElement) {
                countElement.textContent = '0 students';
            }
            return;
        }
        
        let html = '';
        students.forEach((student, index) => {
            html += `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${index + 1}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">${student.name || '-'}</div>
                        <div class="text-xs text-gray-500">${student.gender === 'male' ? 'Male' : 'Female'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                        ${student.icNumber || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            ${student.sportsGames || '-'}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            ${student.clubAssociation || '-'}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            ${student.uniformUnit || '-'}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            ${student.position || '-'}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex gap-2">
                            <button onclick="editStudent(${student.id})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Information">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="editMarks(${student.id})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="Edit Marks">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button onclick="deleteStudent(${student.id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete Student">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
        
        const countElement = document.getElementById('studentCount');
        if (countElement) {
            countElement.textContent = `${students.length} student${students.length !== 1 ? 's' : ''}`;
        }
    }
    
    // Open add student modal
    function openAddStudentModal() {
        editingStudentId = null;
        document.getElementById('modalTitle').textContent = 'Add New Student';
        document.getElementById('studentForm').reset();
        openModal('studentModal');
    }
    
    // Edit student
    function editStudent(studentId) {
        const student = students.find(s => s.id === studentId);
        if (!student) {
            showToast('Student not found', 'error');
            return;
        }
        
        editingStudentId = studentId;
        document.getElementById('modalTitle').textContent = 'Edit Student - ' + student.name;
        
        // Fill form
        document.getElementById('studentName').value = student.name || '';
        document.getElementById('icNumber').value = student.icNumber || '';
        document.getElementById('studentGender').value = student.gender || '';
        document.getElementById('academicSession').value = student.academicSession || '2024/2025';
        document.getElementById('semester').value = student.semester || '1';
        document.getElementById('sportsGames').value = student.sportsGames || '';
        document.getElementById('clubAssociation').value = student.clubAssociation || '';
        document.getElementById('uniformUnit').value = student.uniformUnit || '';
        document.getElementById('position').value = student.position || '';
        
        openModal('studentModal');
    }
    
    // Edit marks
    function editMarks(studentId) {
        const student = students.find(s => s.id === studentId);
        if (!student) {
            showToast('Student not found', 'error');
            return;
        }
        
        quickEditingStudentId = studentId;
        document.getElementById('marksModalTitle').textContent = 'Edit PAJSK Marks - ' + student.name;
        
        // Fill student info
        document.getElementById('marksStudentInfo').innerHTML = `
            <div class="flex items-center gap-3">
                <div>
                    <div class="font-medium text-gray-900">${student.name}</div>
                    <div class="text-sm text-gray-500">${student.icNumber} • Semester ${student.semester}</div>
                </div>
            </div>
        `;
        
        // Fill marks
        document.getElementById('quickUniformMarks').value = student.marks?.uniform || 0;
        document.getElementById('quickClubMarks').value = student.marks?.club || 0;
        document.getElementById('quickSportMarks').value = student.marks?.sport || 0;
        document.getElementById('quickCompetitionMarks').value = student.marks?.competition || 0;
        
        // Update total
        updateQuickMarksTotal();
        openModal('marksModal');
    }
    
    // Update quick marks total
    function updateQuickMarksTotal() {
        const uniform = parseInt(document.getElementById('quickUniformMarks').value) || 0;
        const club = parseInt(document.getElementById('quickClubMarks').value) || 0;
        const sport = parseInt(document.getElementById('quickSportMarks').value) || 0;
        const competition = parseInt(document.getElementById('quickCompetitionMarks').value) || 0;
        
        const totalScore = Math.min(uniform + club + sport + competition, 100);
        document.getElementById('quickTotalScore').textContent = `${totalScore}/100`;
        
        // Color code based on score
        const element = document.getElementById('quickTotalScore');
        element.classList.remove('text-red-600', 'text-yellow-600', 'text-green-600', 'text-indigo-900');
        
        if (totalScore < 50) {
            element.classList.add('text-red-600');
        } else if (totalScore < 70) {
            element.classList.add('text-yellow-600');
        } else if (totalScore < 85) {
            element.classList.add('text-green-600');
        } else {
            element.classList.add('text-indigo-900');
        }
    }
    
    // Save quick marks
    function saveQuickMarks() {
        if (!quickEditingStudentId) {
            showToast('No student selected', 'error');
            return;
        }
        
        const uniform = parseInt(document.getElementById('quickUniformMarks').value) || 0;
        const club = parseInt(document.getElementById('quickClubMarks').value) || 0;
        const sport = parseInt(document.getElementById('quickSportMarks').value) || 0;
        const competition = parseInt(document.getElementById('quickCompetitionMarks').value) || 0;
        
        // Validate marks ranges
        if (uniform < 0 || uniform > 20) {
            showToast('Uniform marks must be between 0-20', 'error');
            return;
        }
        if (club < 0 || club > 20) {
            showToast('Club marks must be between 0-20', 'error');
            return;
        }
        if (sport < 0 || sport > 20) {
            showToast('Sports marks must be between 0-20', 'error');
            return;
        }
        if (competition < 0 || competition > 40) {
            showToast('Competition marks must be between 0-40', 'error');
            return;
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const marksData = {
            uniform: uniform,
            club: club,
            sport: sport,
            competition: competition,
            extra: 0
        };
        
        fetch(`/api/teacher/students/${quickEditingStudentId}/marks`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(marksData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Marks saved successfully', 'success');
                closeModal('marksModal');
                // Reload students from server
                loadStudents();
            } else {
                showToast(data.message || 'Failed to save marks', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving marks:', error);
            showToast('Error saving marks. Please try again.', 'error');
        });
    }
    
    // Delete student
    function deleteStudent(studentId) {
        if (!confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
            return;
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/api/teacher/students/${studentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Student deleted successfully', 'success');
                // Reload students from server
                loadStudents();
            } else {
                showToast(data.message || 'Failed to delete student', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting student:', error);
            showToast('Error deleting student. Please try again.', 'error');
        });
    }
    
    // Setup form validation
    function setupFormValidation() {
        const form = document.getElementById('studentForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            saveStudent();
        });
    }
    
    // Setup marks calculation
    function setupMarksCalculation() {
        const marksInputs = ['quickUniformMarks', 'quickClubMarks', 'quickSportMarks', 'quickCompetitionMarks'];
        marksInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('input', updateQuickMarksTotal);
            }
        });
    }
    
    // Save student
    function saveStudent() {
        const studentData = {
            name: document.getElementById('studentName').value.trim(),
            ic_number: document.getElementById('icNumber').value.trim(),
            gender: document.getElementById('studentGender').value,
            academic_session: document.getElementById('academicSession').value,
            semester: document.getElementById('semester').value,
            sports: document.getElementById('sportsGames').value || null,
            club: document.getElementById('clubAssociation').value || null,
            uniform: document.getElementById('uniformUnit').value || null,
            position: document.getElementById('position').value || null,
            phone: document.getElementById('phone')?.value || null
        };

        // Validate required fields
        if (!studentData.name) {
            showToast('Please enter student name', 'error');
            document.getElementById('studentName').focus();
            return;
        }

        if (!studentData.ic_number) {
            showToast('Please enter IC number', 'error');
            document.getElementById('icNumber').focus();
            return;
        }

        if (!studentData.gender) {
            showToast('Please select gender', 'error');
            document.getElementById('studentGender').focus();
            return;
        }

        // Validate IC number format
        const icRegex = /^\d{6}-\d{2}-\d{4}$/;
        if (!icRegex.test(studentData.ic_number)) {
            showToast('Please enter IC number in correct format: XXXXXX-XX-XXXX', 'error');
            document.getElementById('icNumber').focus();
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const url = editingStudentId 
            ? `/api/teacher/students/${editingStudentId}` 
            : '/api/teacher/students';
        const method = editingStudentId ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(studentData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || (editingStudentId ? 'Student updated successfully' : 'Student added successfully'), 'success');
                closeModal('studentModal');
                // Reload students from server
                loadStudents();
            } else {
                showToast(data.message || 'Failed to save student', 'error');
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error saving student:', error);
            showToast('Error saving student. Please try again.', 'error');
        });
    }
    
    // Modal helpers
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Toast notification function
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
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initializePage);
</script>
@endpush