@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="mb-10">
    <div class="flex items-center gap-4">
        <div class="p-3 bg-indigo-50 rounded-xl">
            <i class="fas fa-chalkboard-teacher text-xl text-indigo-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-black-600 mt-1">{{ Auth::user()->teacher->staff_id ?? '-' }}</p>
            <p class="text-gray-600">Manage Class: <span class="font-semibold text-indigo-900">{{ Auth::user()->teacher->assigned_class ?? '-' }}</span></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Students Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Students</p>
                <p id="totalStudents" class="text-3xl font-bold text-gray-900">-</p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg"><i class="fas fa-users text-blue-600"></i></div>
        </div>
    </div>
    
    <!-- Pending Verification Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="flex items-center justify-between relative z-10">
            <div>
                <p class="text-sm text-gray-500 mb-1">Pending Action</p>
                <p id="pendingCount" class="text-3xl font-black text-gray-900">0</p>
                <p class="text-xs text-gray-500 mt-2">Requires your review</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100"><i class="fas fa-clock text-amber-500 text-xl"></i></div>
        </div>
    </div>
    
    <!-- Student Demographics Card -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <p class="text-sm text-gray-500 mb-1">Student Demographics</p>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div class="text-center">
                <div id="maleCount" class="text-3xl font-bold text-blue-700 mb-2">-</div>
                <div class="text-sm text-gray-700">Male</div>
            </div>
            <div class="text-center">
                <div id="femaleCount" class="text-3xl font-bold text-pink-700 mb-2">-</div>
                <div class="text-sm text-gray-700">Female</div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Leaderboard -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white-50/50">
                <div class="flex items-center gap-2">          
                    <h3 class="text-lg font-bold text-gray-900">Class Leaderboard (Top 10)</h3>
                </div>
            </div>
            <div class="p-4">
                @if(isset($topStudents) && $topStudents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] text-gray-400 font-bold uppercase tracking-wider bg-gray-50/50">
                                <th class="p-3 rounded-l-lg w-16 text-center">Rank</th>
                                <th class="p-3 text-left">Student Name</th>
                                <th class="p-3 text-center">Score</th>
                                <th class="p-3 text-center rounded-r-lg">Grade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($topStudents as $index => $student)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-3 whitespace-nowrap text-center">
                                    @if($index == 0)
                                        <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold mx-auto border border-amber-200">1</div>
                                    @elseif($index == 1)
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold mx-auto border border-slate-200">2</div>
                                    @elseif($index == 2)
                                        <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center font-bold mx-auto border border-orange-100">3</div>
                                    @else
                                        <div class="w-8 h-8 rounded-full text-gray-500 flex items-center justify-center font-bold mx-auto">{{ $index + 1 }}</div>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold text-gray-800">{{ $student->user->name ?? 'Unknown Student' }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->ic_number ?? '-' }}</p>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="font-bold text-indigo-900">{{ number_format($student->marks->first()->total ?? 0, 1) }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        {{ $student->marks->first()->grade ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-10 text-gray-400 text-sm">
                    No leaderboard data available yet. Ensure students have PAJSK marks calculated.
                </div>
                @endif
            </div>
        </div>
        
        <!-- Needs Attention Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-white-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-white-50 flex justify-between items-center bg-white-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-white-500 animate-pulse"></div>
                    <h3 class="text-sm font-bold text-white-900 uppercase tracking-wider">Underperforming Students (D & E)</h3>
                </div>
            </div>
            <div class="p-4 list-none m-0">
                @if(isset($needsAttention) && $needsAttention->count() > 0)
                <div class="space-y-3">
                    @foreach($needsAttention as $student)
                    <div class="flex items-center justify-between p-3 bg-white-70/30 rounded-lg border">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $student->user->name ?? 'Unknown Student' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Total Marks: <span class="font-semibold text-gray-700">{{ number_format($student->marks->first()->total ?? 0, 1) }}</span></p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold {{ ($student->marks->first()->grade ?? '-') == 'E' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' }}">
                            Grade {{ $student->marks->first()->grade ?? '-' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-400 text-sm">
                    <i class="fas fa-check-circle text-emerald-400 text-xl mb-2"></i><br>
                    Great job! All students are performing well.
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Grade Distribution Side Panel -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Grade Distribution</h3>
            <div id="gradeDistribution" class="space-y-4">
                <div class="text-center py-4 text-gray-400 text-sm">Loading grades...</div>
            </div>
        </div>
        
        <!-- This Month's Chart Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">This Month's Activity</h3>
                <span class="text-xs font-semibold bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ now()->format('F') }}</span>
            </div>
            
            <div class="flex items-end h-24 gap-3 mb-4">
                <!-- Approved Bar -->
                <div class="relative w-1/2 flex flex-col justify-end h-full group">
                    <div class="w-full bg-emerald-400 rounded-t-sm transition-all duration-700 ease-out" 
                         style="height: {{ ($monthlyApproved + $monthlyRejected) > 0 ? max(($monthlyApproved / ($monthlyApproved + $monthlyRejected)) * 100, 5) : 0 }}%">
                    </div>
                </div>
                <!-- Rejected Bar -->
                <div class="relative w-1/2 flex flex-col justify-end h-full group">
                    <div class="w-full bg-red-400 rounded-t-sm transition-all duration-700 ease-out" 
                         style="height: {{ ($monthlyApproved + $monthlyRejected) > 0 ? max(($monthlyRejected / ($monthlyApproved + $monthlyRejected)) * 100, 5) : 0 }}%">
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 text-center text-xs">
                <div class="bg-emerald-50 text-emerald-700 py-2 rounded-lg font-bold border border-emerald-100 flex items-center justify-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    {{ $monthlyApproved }} Approved
                </div>
                <div class="bg-red-50 text-red-700 py-2 rounded-lg font-bold border border-red-100 flex items-center justify-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    {{ $monthlyRejected }} Rejected
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize dashboard
    function initializeDashboard() {
        loadDashboardStats();
    }
    
    // Load dashboard statistics
    function loadDashboardStats() {
        // Update counts from server data
        @if(isset($students))
            document.getElementById('totalStudents').textContent = {{ count($students) }};
            document.getElementById('maleCount').textContent = {{ $maleCount ?? 0 }};
            document.getElementById('femaleCount').textContent = {{ $femaleCount ?? 0 }};
            document.getElementById('pendingCount').textContent = {{ $pendingCount ?? 0 }};
            loadGradeDistribution();
        @endif
    }
    
    // Load grade distribution
    function loadGradeDistribution() {
        const container = document.getElementById('gradeDistribution');
        
        @if(isset($gradeDistribution))
            const grades = @json($gradeDistribution ?? []);
            
            let html = '';
            const gradeColors = {
                'A': 'bg-emerald-500',
                'B': 'bg-blue-500',
                'C': 'bg-amber-500',
                'D': 'bg-red-500',
                'E': 'bg-purple-500'
            };
            
            for (const [grade, count] of Object.entries(grades)) {
                html += `
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full ${gradeColors[grade] || 'bg-gray-500'}"></div>
                            Grade ${grade}
                        </span>
                        <span class="font-bold">${count}</span>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        @else
            container.innerHTML = `
                <div class="text-center py-4 text-gray-400 text-sm">
                    Loading grades...
                </div>
            `;
        @endif
    }
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', initializeDashboard);
</script>
@endpush