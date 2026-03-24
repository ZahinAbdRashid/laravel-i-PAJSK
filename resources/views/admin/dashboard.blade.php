@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .stat-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); }
    .card-hover:hover { transform: translateY(-2px); transition: all 0.3s ease; }
</style>
@endpush

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome, {{ Auth::user()->name }}</h1>
                <p class="text-gray-700 mb-3">Head of Co-curricular - SMK Dato' Haji Talib Karim</p>
            </div>
        </div>
    </div>
</div>

<!-- High-level Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="stat-card card-hover p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Students</p>
                <p id="statTotalStudents" class="text-3xl font-bold text-gray-900">0</p>
            </div>
            <div class="p-3 bg-indigo-50 rounded-lg">
                <i class="fas fa-users text-indigo-900"></i>
            </div>
        </div>
    </div>
    <div class="stat-card card-hover p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Grade A &amp; B Students</p>
                <p id="statHighGrades" class="text-3xl font-bold text-emerald-700">0</p>
            </div>
            <div class="p-3 bg-emerald-50 rounded-lg">
                <i class="fas fa-medal text-emerald-700"></i>
            </div>
        </div>
    </div>
    <div class="stat-card card-hover p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Students Missing Any Component</p>
                <p id="statMissingComponents" class="text-3xl font-bold text-amber-600">0</p>
            </div>
            <div class="p-3 bg-amber-50 rounded-lg">
                <i class="fas fa-exclamation-circle text-amber-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Pie Chart: Students per Class -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
        <h3 class="text-lg font-semibold text-indigo-900 mb-4 border-b pb-2">Taburan Pelajar Mengikut Kelas</h3>
        <div class="flex-grow flex items-center justify-center relative min-h-[300px]">
            <canvas id="classPieChart"></canvas>
        </div>
    </div>

    <!-- Bar Chart: Teacher Submissions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
        <h3 class="text-lg font-semibold text-indigo-900 mb-4 border-b pb-2">Status Permohonan Mengikut Guru</h3>
        <div class="flex-grow relative min-h-[300px]">
            <canvas id="teacherBarChart"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Students data
    let students = [];
    
    // Initialize dashboard
    function initializeDashboard() {
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
                updateStudentStats();
            } else {
                console.error('Failed to load students:', data);
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
        });
    }
    
    // Load and render charts
    function loadChartsData() {
        fetch('/admin/api/charts', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPieChart(data.pieChart);
                renderBarChart(data.barChart);
            } else {
                console.error('Failed to load chart data:', data);
            }
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
    }

    // Colors generator
    function generateColors(count) {
        const baseColors = [
            'rgba(79, 70, 229, 0.7)', // Indigo
            'rgba(16, 185, 129, 0.7)', // Emerald
            'rgba(245, 158, 11, 0.7)', // Amber
            'rgba(239, 68, 68, 0.7)', // Red
            'rgba(14, 165, 233, 0.7)', // Sky
            'rgba(168, 85, 247, 0.7)', // Purple
            'rgba(236, 72, 153, 0.7)', // Pink
            'rgba(99, 102, 241, 0.7)', // Indigo Light
            'rgba(34, 197, 94, 0.7)', // Green
            'rgba(249, 115, 22, 0.7)', // Orange
        ];
        
        let colors = [];
        for(let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        return colors;
    }

    let pieChartInstance = null;
    let barChartInstance = null;

    function renderPieChart(chartData) {
        const ctx = document.getElementById('classPieChart').getContext('2d');
        
        if (pieChartInstance) pieChartInstance.destroy();
        
        const backgroundColors = generateColors(chartData.labels.length);
        const borderColors = backgroundColors.map(c => c.replace('0.7)', '1)'));
        
        pieChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' Pelajar';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    function renderBarChart(chartData) {
        const ctx = document.getElementById('teacherBarChart').getContext('2d');
        
        if (barChartInstance) barChartInstance.destroy();
        
        barChartInstance = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                        grid: { display: false }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
    
    // Update student statistics
    function updateStudentStats() {
        const total = students.length;
        document.getElementById('statTotalStudents').textContent = total;

        // Derive grade from totalMarks (same thresholds as student dashboard)
        let highGrades = 0; // A or B
        let missingComponents = 0; // missing any of sports/club/uniform/competition text

        students.forEach(s => {
            const score = Number(s.totalMarks || 0);
            let grade = 'E';
            if (score >= 80) grade = 'A';
            else if (score >= 70) grade = 'B';
            else if (score >= 60) grade = 'C';
            else if (score >= 50) grade = 'D';

            if (grade === 'A' || grade === 'B') {
                highGrades++;
            }

            const hasSports = !!(s.sports && s.sports.trim() !== '');
            const hasClub = !!(s.club && s.club.trim() !== '');
            const hasUniform = !!(s.uniform && s.uniform.trim() !== '');
            // Competition inferred from position/marks is not stored separately, so we treat sports/club/uniform as main components.
            if (!hasSports || !hasClub || !hasUniform) {
                missingComponents++;
            }
        });

        document.getElementById('statHighGrades').textContent = highGrades;
        document.getElementById('statMissingComponents').textContent = missingComponents;
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        initializeDashboard();
        loadChartsData();
    });
</script>
@endpush