@extends('layouts.app')

@section('title', 'Activity History')

@section('content')
<div class="mb-6 sm:mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Recorded Submissions</h1>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-900">
                <i class="fas fa-list-check text-sm"></i>
            </span>
            <div>
                <p class="text-base sm:text-lg font-semibold text-gray-900">All Activities</p>
                <p class="text-xs sm:text-sm text-gray-500">
                    {{ $activities->count() }} total submission{{ $activities->count() === 1 ? '' : 's' }}
                </p>
            </div>
        </div>
    </div>
    {{-- History of all submissions --}}
    <div class="p-3 sm:p-4">
        @if($activities->count() == 0)
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <div class="w-12 h-12 rounded-full border border-dashed border-gray-200 flex items-center justify-center mb-3">
                    <i class="fas fa-clipboard-list text-xl"></i>
                </div>
                <p class="text-sm font-medium">No activities submitted yet</p>
                <p class="text-xs text-gray-500 mt-1">New submissions from the dashboard will appear here.</p>
            </div>
        @else
            <div class="space-y-2 sm:space-y-3 max-h-[520px] overflow-y-auto scrollbar-thin">
                @foreach($activities as $activity)
                    @php
                        $typeText = [
                            'uniform' => 'Uniform Body',
                            'club' => 'Club & Society',
                            'sport' => 'Sports & Games',
                            'competition' => 'Competition',
                            'extra' => 'Extra Curriculum',
                        ][$activity->type] ?? $activity->type;

                        $levelText = [
                            'school' => 'School',
                            'district' => 'District', 
                            'state' => 'State',
                            'national' => 'National',
                            'international' => 'International',
                        ][$activity->level] ?? $activity->level;

                        $achievementText = [
                            'participation' => 'Participation',
                            'third' => 'Third Place',
                            'second' => 'Runner-Up', 
                            'first' => 'Champion',
                        ][$activity->achievement] ?? $activity->achievement;

                        $statusStyles = [
                            'pending' => 'bg-amber-50 text-amber-700 border border-amber-100',
                            'approved' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                            'rejected' => 'bg-rose-50 text-rose-700 border border-rose-100',
                        ];

                        $statusLabel = ucfirst($activity->status);
                        $statusClass = $statusStyles[$activity->status] ?? 'bg-gray-50 text-gray-700 border border-gray-100';
                    @endphp

                    <div class="group rounded-xl border border-gray-100 bg-white/80 hover:bg-white hover:border-indigo-100 hover:shadow-sm transition-all">
                        <div class="p-3 sm:p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs sm:text-sm font-medium text-gray-900">
                                            {{ $typeText }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs sm:text-sm font-medium text-gray-900
                                        </span>
                                        <span class="inline-flex items-center rounded-full {{ $statusClass }} px-2.5 py-0.5 text-xs sm:text-sm font-semibold">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>

                                    <h2 class="text-sm sm:text-base font-semibold text-gray-900 truncate">
                                        {{ $activity->name }}
                                    </h2>

                                    <p class="mt-0.5 text-xs sm:text-sm text-gray-900">
                                        Achievement: <span class="font-medium text-gray-900">{{ $achievementText }}</span>
                                    </p>

                                    <p class="mt-0.5 text-xs sm:text-sm text-gray-900">
                                        Submitted {{ $activity->created_at->format('d M Y') }}
                                        @if($activity->activity_date)
                                            &middot; Activity date {{ $activity->activity_date->format('d M Y') }}
                                        @endif
                                    </p>

                                    @if($activity->status == 'rejected' && $activity->teacher_comment)
                                        <p class="mt-2 text-xs sm:text-sm text-rose-700 bg-rose-50 border border-rose-100 rounded-lg px-2.5 py-1.5">
                                            <span class="font-semibold">Rejection reason:</span>
                                            {{ $activity->teacher_comment }}
                                        </p>
                                    @endif

                                    @php
                                        $statusDetail = null;
                                        if ($activity->status === 'approved' && $activity->approved_at) {
                                            $statusDetail = 'Approved on ' . $activity->approved_at->format('d M Y');
                                        } elseif ($activity->status === 'pending') {
                                            $statusDetail = 'Waiting for teacher verification';
                                        } elseif ($activity->status === 'rejected' && $activity->updated_at) {
                                            $statusDetail = 'Rejected on ' . $activity->updated_at->format('d M Y');
                                        }
                                    @endphp
                                    @if($statusDetail)
                                        <p class="mt-1 text-xs sm:text-sm text-gray-900">
                                            {{ $statusDetail }}
                                        </p>
                                    @endif

                                    @if($activity->documents->count() > 0)
                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            @foreach($activity->documents as $doc)
                                                <a href="{{ route('storage.local', $doc->path) }}" target="_blank"
                                                   class="inline-flex items-center gap-1.5 rounded-full bg-gray-50 hover:bg-gray-100 border border-gray-100 px-3 py-1.5 text-xs sm:text-sm text-gray-700 transition">
                                                    <i class="fas {{ $doc->mime_type === 'application/pdf' ? 'fa-file-pdf text-rose-500' : 'fa-file-image text-indigo-500' }} text-xs sm:text-sm"></i>
                                                    <span class="truncate max-w-[140px]">
                                                        {{ $doc->original_name ?? 'Document' }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                {{-- Edit and Delete Buttons Only for Pending Activities --}}
                                @if($activity->status === 'pending')
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                        <a href="{{ route('student.activities.edit', $activity->id) }}" 
                                           class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs sm:text-sm font-medium text-blue-700 hover:bg-blue-100">
                                            <i class="fas fa-edit text-xs sm:text-sm"></i>
                                            <span>Edit</span>
                                        </a>
                                        
                                        <form method="POST" action="{{ route('student.activities.destroy', $activity->id) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this activity?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1.5 text-xs sm:text-sm font-medium text-rose-700 hover:bg-rose-100">
                                                <i class="fas fa-trash-alt text-xs sm:text-sm"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                              @elseif($activity->status === 'rejected')
    <div class="mt-3 bg-white/90 backdrop-blur-sm p-3 rounded-xl border border-gray-100/80 shadow-sm">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                <div class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></div>
                <h4 class="text-[11px] font-medium text-gray-500 uppercase tracking-wide">Appeal</h4>
            </div>
            <button 
                type="button" 
                onclick="document.getElementById('appeal-form-{{ $activity->id }}').classList.toggle('hidden')" 
                class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-medium text-indigo-600 bg-indigo-50/50 hover:bg-indigo-50 rounded-lg transition-all"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>New</span>
            </button>
        </div>

        <!-- Appeal Form -->
        <form 
            id="appeal-form-{{ $activity->id }}" 
            action="{{ route('student.activities.appeal', $activity->id) }}" 
            method="POST" 
            enctype="multipart/form-data" 
            class="hidden space-y-3 mt-3"
        >
            @csrf
            
            <!-- Appeal Reason -->
            <div class="space-y-1">
                <textarea 
                    name="appeal_comment" 
                    rows="2" 
                    class="w-full px-3 py-1.5 text-xs bg-gray-50/50 border border-gray-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-indigo-500/30 focus:border-indigo-500 transition-all resize-none" 
                    placeholder="Reason for appeal..."
                ></textarea>
            </div>

            <!-- Document Upload -->
            <div class="space-y-1">
                <div class="relative">
                    <input 
                        type="file" 
                        name="documents[]" 
                        multiple 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-[11px] text-gray-400
                            file:mr-2 file:px-2 file:py-1 file:rounded-lg
                            file:border-0 file:text-[11px] file:font-medium
                            file:bg-gray-100 file:text-gray-600
                            hover:file:bg-gray-200
                            file:cursor-pointer
                            cursor-pointer
                            border border-gray-200 rounded-lg
                            py-1 px-2
                            focus:outline-none focus:border-indigo-500"
                    />
                    <p class="text-[10px] text-gray-400 mt-1">
                        PDF, JPG, PNG
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-1.5 px-3 bg-indigo-600 text-white text-[11px] font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500/50 transition-colors"
            >
                Submit Appeal
            </button>
        </form>
    </div>
@endif
                            </div>
                        </div>
                    </div> {{-- Closing tag for the group div --}}
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@endpush