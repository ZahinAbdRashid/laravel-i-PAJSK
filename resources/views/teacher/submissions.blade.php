@extends('layouts.app')

@section('title', 'Pending Class Submissions')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-4">
        <div class="p-3 bg-indigo-50 rounded-xl">
            <i class="fas fa-inbox text-xl text-indigo-900"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pending Submissions</h1>
            <p class="text-gray-600">Review student's extracurricular activity submissions.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-8">
    <!-- New Submissions -->
    <div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">Review Queue</h2>
            <select id="filterType" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-900 focus:border-indigo-900 bg-gray-50 border-0 px-4 py-2">
                <option value="">All Categories</option>
                @foreach($activityTypes as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        
        <div id="pendingActivities" class="flex flex-col gap-3 min-h-[200px]">
            <div class="text-center py-10 text-gray-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-3"></i>
                <p>Loading submissions...</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-8 mt-8">
    <!-- Archived Submissions -->
    <div class="w-full bg-slate-50 rounded-2xl border border-dashed border-gray-200 p-6 opacity-80 hover:opacity-100 transition-opacity">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-sm font-bold text-gray-500 tracking-wider uppercase"><i class="fas fa-archive mr-2"></i> Archived Submissions</h2>
        </div>
        
        <div id="archivedActivities" class="flex flex-col gap-3">
            @if(isset($archivedActivities) && count($archivedActivities) > 0)
                @foreach($archivedActivities as $archive)
                <div class="bg-white border border-gray-100 rounded-xl p-3 flex justify-between items-center opacity-75">
                    <div>
                        <span class="text-[9px] font-bold uppercase tracking-widest text-gray-400 bg-gray-100 px-2 py-0.5 rounded">
                            {{ $activityTypes[$archive->type] ?? $archive->type }}
                        </span>
                        <h4 class="text-sm font-semibold text-gray-600 mt-1">{{ $archive->name }}</h4>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $archive->student->user->name ?? 'Unknown' }}
                        </p>
                    </div>
                    <div>
                        <button onclick="restoreActivity({{ $archive->id }})" class="text-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:text-indigo-900 px-3 py-1.5 rounded-lg font-medium transition">
                            Restore
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-6 text-gray-400 text-sm">No archived submissions.</div>
            @endif
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verifyModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">

        <!-- HEADER -->
        <div class="px-8 pt-8 pb-4 flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Review Activity</h3>
                <p class="text-sm text-gray-500 mt-1" id="modalStudentName">-</p>
            </div>
            <button onclick="closeModal('verifyModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- BODY -->
        <div class="px-8 py-4 max-h-[60vh] overflow-y-auto scrollbar-thin">

            <div id="verifyDetails" class="space-y-6">
                <div class="text-center py-4 text-gray-400">
                    Loading activity details...
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">
                    Teacher's Note
                </label>

                <textarea id="teacherComment" rows="2"
                    class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-900/20 outline-none text-sm transition-all"
                    placeholder="Optional remarks..."></textarea>

                <!-- Quick Comment Templates -->
                <div class="mt-2 flex flex-wrap gap-2">
                    <button type="button" onclick="insertComment('Illegible Certificate')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 px-2 py-1 rounded-full border border-slate-200 transition">Illegible Certificate</button>
                    <button type="button" onclick="insertComment('Missing Signature')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 px-2 py-1 rounded-full border border-slate-200 transition">Missing Signature</button>
                    <button type="button" onclick="insertComment('Wrong Level/Category')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 px-2 py-1 rounded-full border border-slate-200 transition">Wrong Level/Category</button>
                    <button type="button" onclick="insertComment('Not a verifiable extracurricular')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 px-2 py-1 rounded-full border border-slate-200 transition">Not verifiable</button>
                </div>
            </div>

        </div>

        <!-- FOOTER BUTTONS -->
        <div class="p-8 pt-4 flex gap-3">
            <button onclick="archiveActivity()" 
                class="py-3 px-4 text-sm font-semibold text-gray-500 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-700 transition-all">
                <i class="fas fa-archive"></i>
            </button>

            <button onclick="rejectActivity()" 
                class="flex-1 py-3 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all">
                Reject
            </button>

            <button onclick="approveActivity()" 
                class="flex-[2] py-3 text-sm font-semibold text-white bg-indigo-900 rounded-xl hover:bg-indigo-800 shadow-lg shadow-indigo-900/20 transition-all">
                Approve
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    let pendingActivities = @json($activities ?? []);
    let currentActivityId = null;
    
    // Initialize dashboard
    function initializeDashboard() {
        renderPendingActivities();
        document.getElementById('filterType')?.addEventListener('change', renderPendingActivities);
    }
    
    // Render pending activities
    function renderPendingActivities() {
        const container = document.getElementById('pendingActivities');
        const filter = document.getElementById('filterType')?.value || '';
        
        if (!pendingActivities || pendingActivities.length === 0) {
            container.innerHTML = `<div class="text-center py-10 text-gray-400 text-sm">No pending submissions</div>`;
            return;
        }
        
        const filtered = filter ? 
            pendingActivities.filter(a => a.type === filter) : 
            pendingActivities;
        
        if (filtered.length === 0) {
            container.innerHTML = `<div class="text-center py-10 text-gray-400 text-sm">No ${filter} submissions</div>`;
            return;
        }
        
        let html = '';
        filtered.forEach(activity => {
            html += `
                <div class="group bg-white border border-gray-100 rounded-xl p-4 hover:border-indigo-900/20 hover:shadow-sm cursor-pointer transition-all flex justify-between items-center" 
                     onclick="openVerifyModal(${activity.id})">
                    <div>
                        <span class="text-[9px] font-bold uppercase tracking-widest text-indigo-900/50 bg-indigo-900/5 px-2 py-0.5 rounded">
                            ${getActivityTypeText(activity.type)}
                        </span>
                        <h4 class="font-semibold text-gray-800 mt-1">${activity.name || '-'}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">
                            ${activity.student?.user?.name || 'Unknown Student'}
                        </p>
                    </div>
                    <div class="text-right flex items-center gap-4">
                        <span class="text-[10px] text-gray-400 font-medium">
                            ${formatDate(activity.created_at)}
                        </span>
                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-indigo-900 transition-colors text-xs"></i>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Open verification modal
    async function openVerifyModal(id) {
        currentActivityId = id;
        
        try {
            // Fetch activity details
            const response = await fetch(`/teacher/approvals/${id}`);
            const data = await response.json();
            
            if (!response.ok) throw new Error('Failed to load activity');
            
            // Update modal content
            document.getElementById('modalStudentName').textContent = 
                data.student?.user?.name || 'Unknown Student';
            
            const details = document.getElementById('verifyDetails');
            
            let extraLabel = "Achievement";
            let extraValue = data.achievementText || '-';
            
            details.innerHTML = `
                <div class="grid grid-cols-2 gap-y-6">
                    <div class="col-span-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-indigo-900/40">Activity Name</label>
                        <p class="text-lg font-bold text-gray-800 mt-1 leading-tight">
                            ${data.activity.name || '-'}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Category</p>
                        <p class="text-sm font-semibold text-gray-700">${data.typeText}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Level</p>
                        <p class="text-sm font-semibold text-gray-700">${data.levelText}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">${extraLabel}</p>
                        <p class="text-sm font-semibold text-gray-700 capitalize">
                            ${extraValue.replace('_', ' ')}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Submission Date</p>
                        <p class="text-sm font-semibold text-gray-700">
                            ${formatDate(data.activity.created_at)}
                        </p>
                    </div>
                </div>
                ${data.activity.appeal_comment ? `
                <div class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-red-800 mb-1">Appeal Message</p>
                    <p class="text-sm font-medium text-red-900">${data.activity.appeal_comment}</p>
                </div>
                ` : ''}
                <div class="pt-4">
                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-3">Evidence Attached</p>
                    <div class="flex flex-wrap gap-2" id="documentsContainer">
                        ${data.documents && data.documents.length > 0 ? 
                            data.documents.map(doc => `
                                <a href="/storage/${doc.path}" target="_blank" 
                                   class="flex items-center gap-2 px-3 py-2 bg-slate-50 border border-slate-100 rounded-lg hover:bg-white hover:border-indigo-900/30 transition-all cursor-pointer">
                                    <i class="fas ${doc.mime_type === 'application/pdf' ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400'} text-xs"></i>
                                    <span class="text-[11px] font-medium text-gray-600 truncate max-w-[120px]">
                                        ${doc.original_name || 'Document'}
                                    </span>
                                </a>
                            `).join('') : 
                            '<p class="text-sm text-gray-500 italic">No documents attached</p>'
                        }
                    </div>
                </div>
            `;
            
            document.getElementById('teacherComment').value = '';
            document.getElementById('verifyModal').classList.remove('hidden');
            
        } catch (error) {
            console.error('Error loading activity:', error);
            showToast('Failed to load activity details', 'error');
        }
    }
    
    // Close modal
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    
    // Format date
    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-MY', { day: 'numeric', month: 'short' });
        } catch {
            return dateString;
        }
    }
    
    // Get activity type text
    function getActivityTypeText(type) {
        const types = {
            uniform: 'Uniform Body',
            club: 'Club & Society', 
            sport: 'Sports & Games',
            competition: 'Competition',
            extra: 'Extra Curriculum'
        };
        return types[type] || type;
    }
    
    // Approve activity
    async function approveActivity() {
        if (!currentActivityId) {
            showToast('No activity selected', 'error');
            return;
        }
        
        const comment = document.getElementById('teacherComment').value.trim();
        
        try {
            const response = await fetch(`/teacher/approvals/${currentActivityId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ teacher_comment: comment })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove from pending list
                pendingActivities = pendingActivities.filter(a => a.id !== currentActivityId);
                renderPendingActivities();
                updatePendingCount();
                closeModal('verifyModal');
                showToast('Activity approved successfully', 'success');
            } else {
                throw new Error(data.message || 'Approval failed');
            }
            
        } catch (error) {
            console.error('Error approving activity:', error);
            showToast(error.message || 'Failed to approve activity', 'error');
        }
    }
    
    // Reject activity
    async function rejectActivity() {
        if (!currentActivityId) {
            showToast('No activity selected', 'error');
            return;
        }
        
        const comment = document.getElementById('teacherComment').value.trim();
        if (!comment) {
            showToast('Please provide a reason for rejection', 'error');
            document.getElementById('teacherComment').focus();
            return;
        }
        
        try {
            const response = await fetch(`/teacher/approvals/${currentActivityId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ teacher_comment: comment })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove from pending list
                pendingActivities = pendingActivities.filter(a => a.id !== currentActivityId);
                renderPendingActivities();
                updatePendingCount();
                closeModal('verifyModal');
                showToast('Activity rejected', 'info');
            } else {
                throw new Error(data.message || 'Rejection failed');
            }
            
        } catch (error) {
            console.error('Error rejecting activity:', error);
            showToast(error.message || 'Failed to reject activity', 'error');
        }
    }
    
    // Helper to insert quick template comments
    function insertComment(text) {
        const textarea = document.getElementById('teacherComment');
        if (textarea.value) {
            textarea.value += ' ' + text;
        } else {
            textarea.value = text;
        }
        textarea.focus();
    }
    
    // Archive activity
    async function archiveActivity() {
        if (!currentActivityId) return;
        
        if (!confirm('Are you sure you want to archive this submission? It will be moved to the Archived list.')) {
            return;
        }
        
        try {
            const response = await fetch(`/teacher/approvals/${currentActivityId}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Activity archived', 'info');
                // Reload page to reflect fresh lists quickly
                setTimeout(() => window.location.reload(), 500);
            } else {
                throw new Error(data.message || 'Archive failed');
            }
            
        } catch (error) {
            console.error('Error archiving activity:', error);
            showToast(error.message || 'Failed to archive activity', 'error');
        }
    }
    
    // Restore activity
    async function restoreActivity(id) {
        try {
            const response = await fetch(`/teacher/approvals/${id}/restore`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Activity restored to queue', 'success');
                setTimeout(() => window.location.reload(), 500);
            } else {
                throw new Error(data.message || 'Restore failed');
            }
            
        } catch (error) {
            console.error('Error restoring activity:', error);
            showToast(error.message || 'Failed to restore activity', 'error');
        }
    }
    
    // Update pending count visually
    function updatePendingCount() {
        // Just empty stub. No pendingCount id on this view anymore.
    }
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', initializeDashboard);
</script>
@endpush