<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityController extends Controller
{
    /**
     * Display a listing of the activities.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        
        $activities = Activity::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $historyData = $activities->map(function ($activity) {
            return [
                'name' => $activity->name,
                'type' => $activity->type,
                'level' => $activity->level,
                'achievement' => $activity->achievement,
                'status' => $activity->status,
                'activity_date' => $activity->activity_date ? $activity->activity_date->format('Y-m-d') : null,
                'points' => $this->calculateActivityPoints($activity),
            ];
        });
        
        // Mark as notified when viewed
        Activity::where('student_id', $student->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->where('student_notified', false)
            ->update(['student_notified' => true]);
        
        return view('student.history', [
            'activities' => $activities,
            'historyData' => $historyData,
        ]);
    }

    /**
     * Store a newly created activity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:uniform,club,sport,competition,extra',
            'name' => 'required|string|max:255',
            'level' => 'required|in:school,district,state,national,international',
            'achievement' => 'required|in:participation,third,second,first',
            'activity_date' => 'required|date|before_or_equal:today',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents' => 'required|array|min:1',
        ], [
            'documents.required' => 'At least one document is required.',
            'documents.*.required' => 'Each document is required.',
            'activity_date.before_or_equal' => 'Activity date cannot be in the future.',
        ]);

        $user = Auth::user();
        $student = $user->student;

        // Create activity
        $activity = Activity::create([
            'student_id' => $student->id,
            'type' => $request->type,
            'name' => $request->name,
            'level' => $request->level,
            'achievement' => $request->achievement,
            'activity_date' => $request->activity_date,
            'status' => 'pending',
        ]);

        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('documents/activities/' . $activity->id, 'public');
                
                Document::create([
                    'activity_id' => $activity->id,
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Activity submitted successfully! Your activity is pending review.');
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $student = $user->student;
        
        $activity = Activity::where('id', $id)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->firstOrFail();
        
        // Check if activity is still pending (re-check for security)
        if ($activity->status !== 'pending') {
            return redirect()->route('student.dashboard')
                ->with('error', 'Only pending activities can be edited.');
        }
        
        $documents = $activity->documents;
        
        return view('student.edit-activity', compact('activity', 'documents'));
    }

    /**
     * Update the specified activity.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:school,district,state,national,international',
            'achievement' => 'required|in:participation,third,second,first',
            'activity_date' => 'required|date|before_or_equal:today',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        $student = $user->student;
        
        $activity = Activity::where('id', $id)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Update activity
        $activity->update([
            'name' => $request->name,
            'level' => $request->level,
            'achievement' => $request->achievement,
            'activity_date' => $request->activity_date,
        ]);

        // Handle new document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('documents/activities/' . $activity->id, 'public');
                
                Document::create([
                    'activity_id' => $activity->id,
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('student.dashboard')
            ->with('success', 'Activity updated successfully! It will be reviewed again.');
    }

    /**
     * Appeal a rejected activity.
     */
    public function appeal(Request $request, $id)
    {
        $request->validate([
            'appeal_comment' => 'required|string|max:1000',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        $student = $user->student;
        
        $activity = Activity::where('id', $id)
            ->where('student_id', $student->id)
            ->where('status', 'rejected')
            ->firstOrFail();

        // Update activity to pending and add appeal comment
        $activity->update([
            'status' => 'pending',
            'appeal_comment' => $request->appeal_comment,
            'student_notified' => false // reset so teacher review notifies again
        ]);

        // Handle new document uploads if any
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('documents/activities/' . $activity->id, 'public');
                
                Document::create([
                    'activity_id' => $activity->id,
                    'filename' => $file->hashName(),
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('student.activities.index')
            ->with('success', 'Activity appealed successfully! It has been resubmitted for review.');
    }

    /**
     * Remove the specified activity.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $student = $user->student;
        
        $activity = Activity::where('id', $id)
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->firstOrFail();

        // Delete documents and their files
        foreach ($activity->documents as $document) {
            Storage::disk('public')->delete($document->path);
            $document->delete();
        }

        // Delete the folder if empty
        $folderPath = 'documents/activities/' . $activity->id;
        if (Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->deleteDirectory($folderPath);
        }

        $activity->delete();

        return redirect()->route('student.dashboard')
            ->with('success', 'Activity deleted successfully!');
    }

    /**
     * Remove a document from activity.
     */
    public function destroyDocument($id)
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            $document = Document::where('id', $id)
                ->whereHas('activity', function($query) use ($student) {
                    $query->where('student_id', $student->id)
                            ->where('status', 'pending');
                })
                ->firstOrFail();

            // Store activity ID for folder check
            $activityId = $document->activity_id;
            
            // Delete file
            Storage::disk('public')->delete($document->path);
            
            // Delete database record
            $document->delete();
            
            // Check if folder is empty and delete if needed
            $folderPath = 'documents/activities/' . $activityId;
            if (Storage::disk('public')->exists($folderPath)) {
                $remainingFiles = Storage::disk('public')->files($folderPath);
                if (empty($remainingFiles)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document'
            ], 500);
        }
    }

    /**
     * Calculate student's current PAJSK score.
     */
    public function calculateScore()
    {
        $user = Auth::user();
        $student = $user->student;
        
        $approvedActivities = Activity::where('student_id', $student->id)
            ->where('status', 'approved')
            ->get();

        $totalScore = 0;
        $componentScores = [
            'uniform' => 0,
            'club' => 0,
            'sport' => 0,
            'competition' => 0,
            'extra' => 0
        ];

        foreach ($approvedActivities as $activity) {
            $points = $this->calculateActivityPoints($activity);
            
            // Add to total
            $totalScore += $points;
            
            // Add to component score (extra counts towards total but not in component display)
            if (isset($componentScores[$activity->type])) {
                $componentScores[$activity->type] += $points;
            }
        }

        // Apply maximum caps
        $totalScore = min($totalScore, 100);
        $componentScores['uniform'] = min($componentScores['uniform'], 20);
        $componentScores['club'] = min($componentScores['club'], 20);
        $componentScores['sport'] = min($componentScores['sport'], 20);
        $componentScores['competition'] = min($componentScores['competition'], 40);
        
        // Extra activities count towards total but have no individual cap
        // They contribute to total score but aren't shown as a separate component

        $grade = $this->calculateGrade($totalScore);

        return [
            'total_score' => round($totalScore, 1),
            'grade' => $grade,
            'component_scores' => $componentScores,
            'activities_count' => $approvedActivities->count(),
        ];
    }

    /**
     * Download PAJSK score report as PDF.
     */
    public function downloadReport()
    {
        try {
            $user = Auth::user();
            $student = $user->student;

            // Check if student has any approved activities
            $hasApprovedActivities = Activity::where('student_id', $student->id)
                ->where('status', 'approved')
                ->exists();

            if (!$hasApprovedActivities) {
                return redirect()->back()
                    ->with('error', 'No approved activities found. Cannot generate report.');
            }

            $scoreData = $this->calculateScore();

            $approvedActivities = Activity::where('student_id', $student->id)
                ->where('status', 'approved')
                ->with('documents') // Eager load documents
                ->orderBy('activity_date', 'desc')
                ->get();

            // Load view and generate PDF
            $pdf = Pdf::loadView('student.report', [
                'student' => $student,
                'user' => $user,
                'scoreData' => $scoreData,
                'activities' => $approvedActivities,
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            
            // Set PDF options for better rendering
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
            ]);

            $filename = 'PAJSK_Score_Report_' . $student->name . '_' . now()->format('Y-m-d') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to generate PDF report. Please try again.');
        }
    }

    /**
     * Calculate points for a single activity.
     */
    private function calculateActivityPoints($activity)
    {
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

        $basePoints = $levelPoints[$activity->level] ?? 2;
        $multiplier = $achievementMultiplier[$activity->achievement] ?? 1;

        return round($basePoints * $multiplier, 1);
    }

    /**
     * Calculate grade from total score.
     */
    private function calculateGrade($totalScore)
    {
        if ($totalScore >= 80) return 'A';
        if ($totalScore >= 70) return 'B';
        if ($totalScore >= 60) return 'C';
        if ($totalScore >= 50) return 'D';
        return 'E';
    }

    /**
     * Display student dashboard with activities and scores.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->student;
        
        // Get recent activities (last 5)
        $recentActivities = Activity::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get pending count
        $pendingCount = Activity::where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();
        
        // Get score data
        $scoreData = $this->calculateScore();
        
        return view('student.dashboard', [
            'student' => $student,
            'user' => $user,
            'recentActivities' => $recentActivities,
            'pendingCount' => $pendingCount,
            'scoreData' => $scoreData,
        ]);
    }
}