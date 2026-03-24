@extends('layouts.app')

@section('title', 'Student Dashboard')

@push('styles')
<style>
    .upload-area { transition: all 0.3s ease; }
    .upload-area.dragover { border-color: #162660; background-color: #f8fafc; }
    .activity-card:hover { transform: translateY(-2px); }
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #d1fae5; color: #065f46; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
</style>
@endpush

@section('content')
@php
    $user = Auth::user();
    $student = $user->student;
    $activities = $student->activities()->orderBy('created_at', 'desc')->get();
    $ruleSuggestions = \App\Models\SuggestionRule::getSuggestionsForStudent($student);
    
    // Calculate score
    $totalScore = 0;
    $componentScores = [
        'uniform' => 0,
        'club' => 0,
        'sport' => 0,
        'competition' => 0,
        'extra' => 0
    ];
    
    $approvedActivities = $activities->where('status', 'approved');
    foreach ($approvedActivities as $activity) {
        $levelPoints = [
            'school' => 2,
            'district' => 4,
            'state' => 6,
            'national' => 8,
            'international' => 10
        ];
        
        $achievementMultiplier = [
            'participation' => 1,
            'third' => 1.5,
            'second' => 2,
            'first' => 3
        ];
        
        $points = ($levelPoints[$activity->level] ?? 2) * ($achievementMultiplier[$activity->achievement] ?? 1);
        $totalScore += $points;
        
        if (isset($componentScores[$activity->type])) {
            $componentScores[$activity->type] += $points;
        }
    }
    
    // Cap scores
    $totalScore = min($totalScore, 100);
    $componentScores['uniform'] = min($componentScores['uniform'], 20);
    $componentScores['club'] = min($componentScores['club'], 20);
    $componentScores['sport'] = min($componentScores['sport'], 20);
    $componentScores['competition'] = min($componentScores['competition'], 40);
    
    // Calculate grade
    if ($totalScore >= 80) $grade = 'A';
    elseif ($totalScore >= 70) $grade = 'B';
    elseif ($totalScore >= 60) $grade = 'C';
    elseif ($totalScore >= 50) $grade = 'D';
    else $grade = 'E';
@endphp

<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="p-2 sm:p-3 bg-indigo-900 bg-opacity-10 rounded-xl">
                <i class="fas fa-graduation-cap text-xl sm:text-2xl text-indigo-900"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Welcome, {{ $user->name }}</h1>
                <p class="text-xs sm:text-sm font-semibold text-gray-800 mt-1">
                    <span class="block sm:inline">Class: {{ $student->teacher->assigned_class ?? '-' }}</span>
                    <span class="hidden sm:inline"> | </span>
                    <span class="block sm:inline">Session: {{ $student->academic_session ?? '-' }}</span>
                    <span class="hidden sm:inline"> | </span>
                    <span class="block sm:inline">Semester: {{ $student->semester ?? '-' }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
    <!-- Left Column -->
    <div class="space-y-4 sm:space-y-6 lg:space-y-8">
        <!-- Submission Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
            <div class="border-b border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
                <h3 class="text-base sm:text-lg font-semibold text-indigo-900 flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Submit New Activity
                </h3>
            </div>
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                <form id="activityForm" method="POST" action="{{ route('student.activities.store') }}" 
                      enctype="multipart/form-data">
                    @csrf
                    
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
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Activity Type *</label>
                        <select name="type" id="activityType" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition bg-white">
                            <option value="">Please Select</option>
                            <option value="uniform">Uniform Body</option>
                            <option value="club">Club & Society</option>
                            <option value="sport">Sports & Games</option>
                            <option value="competition">Competition</option>
                            <option value="extra">Extra Curriculum</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Activity Name *</label>
                        <input type="text" name="name" id="activityName" placeholder="Enter Activity Name" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Level *</label>
                            <select name="level" id="activityLevel" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition bg-white">
                                <option value="school">School</option>
                                <option value="district">District</option>
                                <option value="state">State</option>
                                <option value="national">National</option>
                                <option value="international">International</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Achievement *</label>
                            <select name="achievement" id="achievement" required
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 outline-none bg-white">
                                <option value="participation">Participation</option>
                                <option value="third">Third Place</option>
                                <option value="second">Runner-Up</option>
                                <option value="first">Champion</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Activity Date *</label>
                        <input type="date" name="activity_date" id="activityDate" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none transition"
                            value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="space-y-3 sm:space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <h4 class="text-sm sm:text-md font-semibold text-gray-700 flex items-center gap-2">
                                <i class="fas fa-paperclip"></i>
                                Upload Documents <span class="text-red-500">*</span>
                            </h4>
                            <span class="text-xs text-gray-500">Max: 5MB each (PDF, JPG, PNG)</span>
                        </div>
                        
                        <div id="uploadArea" class="upload-area border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center cursor-pointer hover:border-indigo-900 hover:bg-blue-50 transition">
                            <i class="fas fa-cloud-upload-alt text-2xl sm:text-3xl text-indigo-900 mb-2 sm:mb-3"></i>
                            <p class="text-sm sm:text-base font-medium text-gray-700 mb-1">Click or Drag Files Here</p>
                            <p class="text-xs sm:text-sm text-gray-500">At least one document is required</p>
                            <input type="file" name="documents[]" id="fileInput" class="hidden" 
                            accept=".pdf,.jpg,.jpeg,.png" multiple required>
                        </div>
                        
                        <div id="filePreviewContainer" class="space-y-2 max-h-40 overflow-y-auto scrollbar-thin"></div>
                    </div>

                    <div id="buttonContainer">
                        <button type="submit" class="w-full py-3 sm:py-3.5 bg-indigo-900 text-white font-semibold rounded-lg hover:bg-indigo-800 transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            Submit Submission
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- What-if Score Calculator -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
                <h3 class="text-base sm:text-lg font-semibold text-indigo-900 flex items-center gap-2">
                    <i class="fas fa-calculator"></i>
                    What-if Score Calculator
                </h3>
            </div>
            <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Activity Type</label>
                        <select id="whatIfType"
                            class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            <option value="uniform">Uniform Body</option>
                            <option value="club">Club & Society</option>
                            <option value="sport">Sports & Games</option>
                            <option value="competition">Competition</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Level</label>
                        <select id="whatIfLevel"
                            class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            <option value="school">School</option>
                            <option value="district">District</option>
                            <option value="state">State</option>
                            <option value="national">National</option>
                            <option value="international">International</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Achievement</label>
                        <select id="whatIfAchievement"
                            class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-indigo-900 focus:border-indigo-900 outline-none bg-white">
                            <option value="participation">Participation</option>
                            <option value="third">Third Place</option>
                            <option value="second">Runner-Up</option>
                            <option value="first">Champion</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="whatIfBtn"
                            class="w-full py-2.5 sm:py-3 bg-indigo-900 text-white text-xs sm:text-sm font-semibold rounded-lg hover:bg-indigo-800 transition flex items-center justify-center gap-2">
                            <i class="fas fa-magic"></i>
                            Calculate Impact
                        </button>
                    </div>
                </div>

                <div id="whatIfResult" class="bg-indigo-50 border border-indigo-100 rounded-lg px-3 sm:px-4 py-3 text-xs sm:text-sm text-indigo-900 hidden">
                    <div class="font-semibold mb-1">
                        Estimated Impact
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                        <div>
                            <span>Score: </span>
                            <span class="font-semibold" id="whatIfScoreText">{{ $totalScore }}/100 → {{ $totalScore }}/100</span>
                        </div>
                        <div>
                            <span>Grade: </span>
                            <span class="font-semibold" id="whatIfGradeText">{{ $grade }} → {{ $grade }}</span>
                        </div>
                    </div>
                    <p class="mt-1 text-[11px] text-indigo-800">
                        This is only an estimate. Final marks depend on teacher verification and PAJSK rules.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="space-y-4 sm:space-y-6 lg:space-y-8">
        <!-- Current PAJSK Score Results -->
        <div class="bg-gradient-to-br from-indigo-900 to-indigo-700 rounded-xl p-4 sm:p-6 text-white">
            <div class="text-center mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-bold mb-2 flex items-center justify-center gap-2">
                    <i class="fas fa-medal"></i>
                    <span class="text-sm sm:text-base md:text-xl">Current PAJSK Score Results</span>
                </h3>
            </div>
            
            <div class="text-center mb-4 sm:mb-6">
                <div id="overallGrade" class="text-5xl sm:text-6xl font-bold text-yellow-300 mb-2">{{ $grade }}</div>
                <div id="scoreText" class="text-base sm:text-lg opacity-90">Score: {{ $totalScore }}/100</div>
            </div>

            <div class="mb-6 sm:mb-8">
                <div class="flex justify-between text-xs sm:text-sm mb-1">
                    <span>Score Progress</span>
                    <span id="scorePercentage">{{ $totalScore }}%</span>
                </div>
                <div class="h-2 bg-white bg-opacity-20 rounded-full overflow-hidden">
                    <div id="scoreProgress" class="h-full bg-gradient-to-r from-cyan-400 to-green-400 rounded-full" 
                        style="width: {{ $totalScore }}%"></div>
                </div>
            </div>

            <div class="space-y-3 mb-4 sm:mb-6">
                <h4 class="text-sm sm:text-base font-semibold border-b border-white border-opacity-30 pb-2">Scores by Component:</h4>
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    <div class="bg-white bg-opacity-10 p-2 sm:p-3 rounded-lg">
                        <div class="text-xs sm:text-sm opacity-90">Uniform Body</div>
                        <div id="uniformScore" class="font-semibold text-sm sm:text-base">{{ $componentScores['uniform'] }}/20</div>
                    </div>
                    <div class="bg-white bg-opacity-10 p-2 sm:p-3 rounded-lg">
                        <div class="text-xs sm:text-sm opacity-90">Club & Society</div>
                        <div id="clubScore" class="font-semibold text-sm sm:text-base">{{ $componentScores['club'] }}/20</div>
                    </div>
                    <div class="bg-white bg-opacity-10 p-2 sm:p-3 rounded-lg">
                        <div class="text-xs sm:text-sm opacity-90">Sports & Games</div>
                        <div id="sportScore" class="font-semibold text-sm sm:text-base">{{ $componentScores['sport'] }}/20</div>
                    </div>
                    <div class="bg-white bg-opacity-10 p-2 sm:p-3 rounded-lg">
                        <div class="text-xs sm:text-sm opacity-90">Competition</div>
                        <div id="competitionScore" class="font-semibold text-sm sm:text-base">{{ $componentScores['competition'] }}/40</div>
                    </div>
                </div>
            </div>
            <div class="text-center">
            <button type="button"
                    onclick="window.location.href='{{ route('student.report') }}'"
                    class="mt-1 inline-flex items-center gap-1.5 px-5 py-1.5 rounded-full bg-white/10 border border-white/30 text-[11px] sm:text-xs font-medium hover:bg-white/20">
                    <i class="fas fa-download"></i>
                    Download Score Report 
                </button>
            </div>
        </div>

        <!-- Grade Improvement Suggestions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 px-4 sm:px-6 py-3 sm:py-4">
                <h3 class="text-base sm:text-lg font-semibold text-indigo-900 flex items-center gap-2">
                    <i class="fas fa-lightbulb"></i>
                    Grade Improvement Suggestions
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                <div id="strategySuggestions" class="text-sm text-gray-600">
                    @if($ruleSuggestions->count() > 0)
                    <div class="space-y-3">
                        @foreach($ruleSuggestions as $suggestion)
                        <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
                            <div class="flex items-center justify-between mb-1">
                                <div class="font-semibold text-indigo-900">
                                    {{ $suggestion['activityName'] ?? 'Suggested Activity' }}
                                </div>
                                <div class="text-xs text-indigo-800 flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full bg-white border border-indigo-100">
                                        {{ ucfirst($suggestion['level'] ?? 'school') }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ ucfirst($suggestion['achievement'] ?? 'participation') }}</span>
                                    @if(isset($suggestion['points']))
                                    <span>•</span>
                                    <span class="font-semibold">+{{ $suggestion['points'] }} pts</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs sm:text-sm text-gray-700">
                                {{ $suggestion['message'] ?? 'This activity can help improve your PAJSK score.' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- FILE UPLOAD HANDLERS ---
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const filePreviewContainer = document.getElementById('filePreviewContainer');

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
            <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-red-500 transition">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        filePreviewContainer.appendChild(preview);
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Set max date to today for activity date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('activityDate').max = today;

        // What-if calculator
        const whatIfBtn = document.getElementById('whatIfBtn');
        const whatIfResult = document.getElementById('whatIfResult');
        const baseScore = {{ $totalScore }};

        function gradeFromScore(score) {
            if (score >= 80) return 'A';
            if (score >= 70) return 'B';
            if (score >= 60) return 'C';
            if (score >= 50) return 'D';
            return 'E';
        }

        function calculateWhatIf() {
            const level = document.getElementById('whatIfLevel').value;
            const achievement = document.getElementById('whatIfAchievement').value;

            const levelPoints = {
                school: 2,
                district: 4,
                state: 6,
                national: 8,
                international: 10
            };

            const achievementMultiplier = {
                participation: 1,
                third: 1.5,
                second: 2,
                first: 3
            };

            const lp = levelPoints[level] ?? 2;
            const mult = achievementMultiplier[achievement] ?? 1;
            const added = lp * mult;

            let newScore = baseScore + added;
            if (newScore > 100) newScore = 100;

            const oldGrade = gradeFromScore(baseScore);
            const newGrade = gradeFromScore(newScore);

            document.getElementById('whatIfScoreText').textContent = `${baseScore}/100 → ${newScore}/100`;
            document.getElementById('whatIfGradeText').textContent = `${oldGrade} → ${newGrade}`;
            whatIfResult.classList.remove('hidden');
        }

        if (whatIfBtn) {
            whatIfBtn.addEventListener('click', calculateWhatIf);
        }
    });
</script>
@endpush