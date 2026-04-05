@extends('layouts.app')

@section('title', 'Submissions Log')

@section('content')
<div class="mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Submissions Log</h1>
                <p class="text-gray-700 mb-0">Monitor all approved and rejected submissions from teachers.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-sm">
                    <th class="py-4 px-6 font-semibold text-gray-600">Date Logged</th>
                    <th class="py-4 px-6 font-semibold text-gray-600">Student Info</th>
                    <th class="py-4 px-6 font-semibold text-gray-600">Activity Details</th>
                    <th class="py-4 px-6 font-semibold text-gray-600">Evaluated By</th>
                    <th class="py-4 px-6 font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6 align-top">
                            <span class="text-sm font-medium text-gray-900">{{ $activity->updated_at->format('d M Y') }}</span>
                            <div class="text-xs text-gray-500 mt-1">{{ $activity->updated_at->format('h:i A') }}</div>
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="text-sm font-medium text-gray-900">{{ optional($activity->student->user)->name ?? 'Unknown Student' }}</div>
                            <div class="text-xs text-gray-600 mt-1">
                                Class: <span class="font-medium text-gray-800">{{ optional($activity->student->teacher)->assigned_class ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="text-sm text-gray-900">
                                <span class="font-medium">{{ $activity->name }}</span>
                                <span class="text-xs px-2 py-0.5 ml-2 bg-gray-100 text-gray-600 rounded-full border border-gray-200 capitalize">
                                    {{ $activity->type }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Level: <span class="capitalize">{{ $activity->level }}</span> | 
                                Achievement: <span class="capitalize">{{ $activity->achievement }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 align-top">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-chalkboard-teacher text-gray-400 mr-1"></i>
                                {{ optional($activity->approvedBy->user)->name ?? 'Unknown Teacher' }}
                            </div>
                            @if($activity->teacher_comment)
                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded border border-gray-100 inline-block max-w-xs truncate" title="{{ $activity->teacher_comment }}">
                                    <i class="fas fa-comment-alt text-gray-400 mr-1"></i> "{{ \Illuminate\Support\Str::limit($activity->teacher_comment, 30) }}"
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-6 align-top">
                            @if($activity->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1.5"></i> Approved
                                </span>
                            @elseif($activity->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times-circle mr-1.5"></i> Rejected
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                            <i class="fas fa-clipboard text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-base">No log records found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($activities->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $activities->links() }}
        </div>
    @endif
</div>
@endsection
