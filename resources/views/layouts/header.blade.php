@php
    $user = Auth::user();
    $role = $user ? $user->role : null;
    $unreadCount = 0;
    $teacherUnreadCount = 0;
    
    // Student unread notifications
    if ($role === 'student' && $user->student) {
        $unreadCount = \App\Models\Activity::where('student_id', $user->student->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('student_notified', false)
            ->count();
    }
    
    // Teacher unread submissions
    if ($role === 'teacher' && $user->teacher) {
        $studentIds = \App\Models\Student::where('teacher_id', $user->teacher->id)->pluck('id');
        $teacherUnreadCount = \App\Models\Activity::whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->count();
    }
@endphp

<header class="bg-white px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo and Brand -->
        <div class="flex items-center gap-2 sm:gap-4">
            <img src="{{ asset('images/logo sekolah.png') }}" alt="School Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-cover rounded-lg">
            <div>
                <h1 class="text-base sm:text-lg font-bold text-indigo-900">i-PAJSK</h1>
                <p class="text-xs text-gray-600 hidden sm:block">SMK Dato' Haji Talib Karim</p>
            </div>
        </div>

        <!-- Desktop Navigation Menu -->
        <nav class="hidden md:flex gap-1">
            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="flex items-center gap-2 {{ request()->routeIs('admin.teachers.*') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Manage Teachers</span>
                </a>
                <a href="{{ route('admin.students.backup') }}" class="flex items-center gap-2 {{ request()->routeIs('admin.students.backup') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-user-graduate"></i>
                    <span>Student Data</span>
                </a>
                <a href="{{ route('admin.suggestions.index') }}" class="flex items-center gap-2 {{ request()->routeIs('admin.suggestions.*') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-lightbulb"></i>
                    <span>Suggestions</span>
                </a>
                <a href="{{ route('admin.submissions.log') }}" class="flex items-center gap-2 {{ request()->routeIs('admin.submissions.log') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Submissions Log</span>
                </a>
            @endif

            @if($role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-2 {{ request()->routeIs('teacher.dashboard') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('teacher.approvals.index') }}" class="flex items-center gap-2 {{ request()->routeIs('teacher.approvals.index') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium relative">
                    <i class="fas fa-inbox"></i>
                    <span>Submissions</span>
                    @if($teacherUnreadCount > 0)
                        <span class="absolute top-1 right-2 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $teacherUnreadCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('teacher.students.index') }}" class="flex items-center gap-2 {{ request()->routeIs('teacher.students.*') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-users"></i>
                    <span>Manage Students</span>
                </a>
            @endif

            @if($role === 'student')
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2 {{ request()->routeIs('student.dashboard') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('student.activities.index') }}" class="flex items-center gap-2 {{ request()->routeIs('student.activities.index') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium relative">
                    <i class="fas fa-list-check"></i>
                    <span>History</span>
                    @if($unreadCount > 0)
                        <span class="absolute top-1 right-2 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            @endif

            @auth
                @if($role !== 'admin')
                <a href="{{ route('profile') }}" class="flex items-center gap-2 {{ request()->routeIs('profile') ? 'nav-active' : 'text-gray-700' }} px-4 py-2 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-red-600 px-4 py-2 rounded-lg hover:bg-red-50 transition font-medium">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @endauth
        </nav>

        <!-- Mobile Menu Button -->
        <button id="mobileMenuBtn" class="md:hidden p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Mobile Navigation Menu -->
    <nav id="mobileMenu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-100">
        <div class="flex flex-col gap-2 pt-4">
            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.teachers.*') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-chalkboard-teacher w-5"></i>
                    <span>Manage Teachers</span>
                </a>
                <a href="{{ route('admin.students.backup') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.students.backup') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-user-graduate w-5"></i>
                    <span>Student Data</span>
                </a>
                <a href="{{ route('admin.suggestions.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.suggestions.*') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-lightbulb w-5"></i>
                    <span>Suggestions</span>
                </a>
                <a href="{{ route('admin.submissions.log') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.submissions.log') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span>Submissions Log</span>
                </a>
            @endif

            @if($role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3 {{ request()->routeIs('teacher.dashboard') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('teacher.approvals.index') }}" class="flex items-center gap-3 {{ request()->routeIs('teacher.approvals.index') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium relative">
                    <i class="fas fa-inbox w-5"></i>
                    <span>Submissions</span>
                    @if($teacherUnreadCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $teacherUnreadCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('teacher.students.index') }}" class="flex items-center gap-3 {{ request()->routeIs('teacher.students.*') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-users w-5"></i>
                    <span>Manage Students</span>
                </a>
            @endif

            @if($role === 'student')
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 {{ request()->routeIs('student.dashboard') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-home w-5"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('student.activities.index') }}" class="flex items-center gap-3 {{ request()->routeIs('student.activities.index') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium relative">
                    <i class="fas fa-list-check w-5"></i>
                    <span>Recorded</span>
                    @if($unreadCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            @endif

            @auth
                @if($role !== 'admin')
                <a href="{{ route('profile') }}" class="flex items-center gap-3 {{ request()->routeIs('profile') ? 'nav-active' : 'text-gray-700' }} px-4 py-3 rounded-lg hover:bg-gray-100 transition font-medium">
                    <i class="fas fa-user w-5"></i>
                    <span>Profile</span>
                </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 text-red-600 px-4 py-3 rounded-lg hover:bg-red-50 transition font-medium text-left">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @endauth
        </div>
    </nav>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                const icon = this.querySelector('i');
                if (mobileMenu.classList.contains('hidden')) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                } else {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            });
        }
    });
</script>