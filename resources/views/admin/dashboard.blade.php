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

<!-- Dynamic Charts Container -->
<div id="dynamicChartsContainer" class="mt-8 relative">
    
    <!-- PIE CHARTS (GENDER) -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-venus-mars text-indigo-500"></i> Gender Distribution per Class
            </h2>
        </div>
        <div id="pieChartsGrid" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Dynamically populated -->
        </div>
    </div>

    <!-- MONTHLY STATUS -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-bar text-indigo-500"></i> Monthly Approval Status
            </h2>
        </div>
        <div id="barChartsGrid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Dynamically populated -->
        </div>
    </div>

    <!-- GRADE CHART -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-award text-indigo-500"></i> Grade Distribution
            </h2>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex-grow relative min-h-[350px]">
            <canvas id="gradeChart"></canvas>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                renderPieCharts(data.pieCharts);
                renderBarCharts(data.barCharts);
                renderGradeChart(data.gradeChart);
            } else {
                console.error('Failed to load chart data:', data);
            }
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
    }

    /* COMMON OPTIONS (clean minimalist look) */
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#6b7280',
                    font: { size: 12, family: "'Inter', sans-serif" }
                }
            }
        }
    };

    /* PIE CHARTS RENDERER */
    function renderPieCharts(pieChartsData) {
        const container = document.getElementById('pieChartsGrid');
        container.innerHTML = ''; // clear

        pieChartsData.forEach((data, index) => {
            if(data.male === 0 && data.female === 0) return; // skip empty
            
            const chartId = `pieChart_${index}`;
            const colItem = document.createElement('div');
            colItem.className = 'bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover transition-all';
            colItem.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Class ${data.className}</h3>
                    <div class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs rounded-lg font-medium">${data.male + data.female} Students</div>
                </div>
                <div class="relative min-h-[220px]">
                    <canvas id="${chartId}"></canvas>
                </div>
            `;
            container.appendChild(colItem);

            new Chart(document.getElementById(chartId), {
                type: 'doughnut',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        data: [data.male, data.female],
                        backgroundColor: ['#3b82f6', '#f472b6'], // Blue, Pink
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '65%',
                }
            });
        });
    }

    /* BAR CHARTS RENDERER */
    function renderBarCharts(barChartsData) {
        const container = document.getElementById('barChartsGrid');
        container.innerHTML = '';

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        barChartsData.forEach((data, index) => {
            // Check if there is any data
            const hasData = data.approved.some(v => v > 0) || data.rejected.some(v => v > 0);
            if (!hasData) return;

            const chartId = `barChart_${index}`;
            const colItem = document.createElement('div');
            colItem.className = 'bg-white p-5 rounded-2xl shadow-sm border border-gray-100 card-hover transition-all';
            colItem.innerHTML = `
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Class ${data.className}</h3>
                </div>
                <div class="relative min-h-[250px]">
                    <canvas id="${chartId}"></canvas>
                </div>
            `;
            container.appendChild(colItem);

            new Chart(document.getElementById(chartId), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Approved',
                            data: data.approved,
                            backgroundColor: '#10b981', // Emerald 500
                            borderRadius: 4,
                            barPercentage: 0.6
                        },
                        {
                            label: 'Rejected',
                            data: data.rejected,
                            backgroundColor: '#ef4444', // Red 500
                            borderRadius: 4,
                            barPercentage: 0.6
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            grid: { color: '#f3f4f6', drawBorder: false },
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    }

    /* GRADE CHART RENDERER */
    function renderGradeChart(gradeData) {
        new Chart(document.getElementById('gradeChart'), {
            type: 'bar',
            data: {
                labels: gradeData.labels,
                datasets: [{
                    label: 'Students',
                    data: gradeData.data,
                    backgroundColor: [
                        '#3b82f6', // blue
                        '#6366f1', // indigo
                        '#8b5cf6', // violet
                        '#d946ef', // fuchsia
                        '#f43f5e'  // rose
                    ],
                    borderRadius: 8,
                    barPercentage: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        grid: { color: '#f3f4f6', drawBorder: false },
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
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
            if (!hasSports || !hasClub || !hasUniform) {
                missingComponents++;
            }
        });

        const highGradesEl = document.getElementById('statHighGrades');
        if (highGradesEl) highGradesEl.textContent = highGrades;
        
        const missingEl = document.getElementById('statMissingComponents');
        if (missingEl) missingEl.textContent = missingComponents;
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        initializeDashboard();
        loadChartsData();
    });
</script>
@endpush