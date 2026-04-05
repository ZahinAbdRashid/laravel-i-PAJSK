<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Mark;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    // Display pending activities for approval on dedicated Submissions page.
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        // Get students in teacher's class
        $studentIds = Student::where('teacher_id', $teacher->id)
            ->pluck('id');
        
        // Get pending activities from teacher's students
        $activities = Activity::with(['student.user', 'documents'])
            ->whereIn('student_id', $studentIds)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by type for filtering
        $activityTypes = [
            'uniform' => 'Uniform Body',
            'club' => 'Club & Society',
            'sport' => 'Sports & Games',
            'competition' => 'Competition',
            'extra' => 'Extra Curriculum'
        ];
        
        // Get archived (soft-deleted) activities
        $archivedActivities = Activity::onlyTrashed()
            ->with(['student.user', 'documents'])
            ->whereIn('student_id', $studentIds)
            ->orderBy('deleted_at', 'desc')
            ->get();
        
        return view('teacher.submissions', compact('activities', 'activityTypes', 'archivedActivities'));
    }

    // Show activity details for review.
    public function show($id)
    {
        try {
            $teacher = Auth::user()->teacher;
            
            $activity = Activity::with(['student.user', 'student.marks', 'documents'])
                ->where('id', $id)
                ->whereHas('student', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })
                ->firstOrFail();
            
            $levelText = [
                'school' => 'School',
                'district' => 'District',
                'state' => 'State',
                'national' => 'National',
                'international' => 'International'
            ];
            
            $achievementText = [
                'participation' => 'Participation',
                'third' => 'Third Place',
                'second' => 'Runner-Up',
                'first' => 'Champion'
            ];
            
            return response()->json([
                'success' => true,
                'activity' => $activity,
                'levelText' => $levelText[$activity->level] ?? $activity->level,
                'achievementText' => $achievementText[$activity->achievement] ?? $activity->achievement,
                'typeText' => $this->getActivityTypeText($activity->type),
                'student' => $activity->student,
                'documents' => $activity->documents
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load activity: ' . $e->getMessage()
            ], 404);
        }
    }

    // Approve an activity.
    public function approve(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'teacher_comment' => 'nullable|string|max:500'
            ]);
            
            $teacher = Auth::user()->teacher;
            
            DB::transaction(function () use ($teacher, $id, $validated) {
                // Get and update activity
                $activity = Activity::where('id', $id)
                    ->where('status', 'pending')
                    ->whereHas('student', function($query) use ($teacher) {
                        $query->where('teacher_id', $teacher->id);
                    })
                    ->firstOrFail();
                
                $activity->update([
                    'status' => 'approved',
                    'teacher_comment' => $validated['teacher_comment'] ?? null,
                    'approved_by' => $teacher->id,
                    'approved_at' => now(),
                ]);
                
                // Update student marks
                $this->updateStudentMarks($activity->student_id);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Activity approved successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve activity: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reject an activity.
    public function reject(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'teacher_comment' => 'required|string|min:10|max:500'
            ]);
            
            $teacher = Auth::user()->teacher;
            
            $activity = Activity::where('id', $id)
                ->where('status', 'pending')
                ->whereHas('student', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })
                ->firstOrFail();
            
            $activity->update([
                'status' => 'rejected',
                'teacher_comment' => $validated['teacher_comment'],
                'approved_by' => $teacher->id,
                'approved_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Activity rejected.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject activity: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Archive an activity (soft delete).
    public function archive($id)
    {
        try {
            $teacher = Auth::user()->teacher;
            
            $activity = Activity::where('id', $id)
                ->where('status', 'pending')
                ->whereHas('student', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })
                ->firstOrFail();
                
            $activity->delete(); // Soft delete
            
            return response()->json([
                'success' => true,
                'message' => 'Activity archived successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive activity.'
            ], 500);
        }
    }
    
    // Restore an archived activity.
    public function restore($id)
    {
        try {
            $teacher = Auth::user()->teacher;
            
            $activity = Activity::onlyTrashed()
                ->where('id', $id)
                ->whereHas('student', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })
                ->firstOrFail();
                
            $activity->restore();
            
            return response()->json([
                'success' => true,
                'message' => 'Activity restored successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore activity.'
            ], 500);
        }
    }

    // Update student marks after activity approval.
    private function updateStudentMarks($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            
            // Get all approved activities for this student
            $approvedActivities = Activity::where('student_id', $studentId)
                ->where('status', 'approved')
                ->get();
            
            // Calculate scores
            $componentScores = [
                'uniform' => 0,
                'club' => 0,
                'sport' => 0,
                'competition' => 0,
                'extra' => 0
            ];
            
            foreach ($approvedActivities as $activity) {
                $points = $this->calculateActivityPoints($activity);
                
                if (isset($componentScores[$activity->type])) {
                    $componentScores[$activity->type] += $points;
                }
            }
            
            // Cap scores at maximum
            $componentScores['uniform'] = min($componentScores['uniform'], 20);
            $componentScores['club'] = min($componentScores['club'], 20);
            $componentScores['sport'] = min($componentScores['sport'], 20);
            $componentScores['competition'] = min($componentScores['competition'], 40);
            $componentScores['extra'] = min($componentScores['extra'], 100);
            
            $totalScore = array_sum($componentScores);
            $totalScore = min($totalScore, 100);
            
            $grade = $this->calculateGrade($totalScore);
            
            // Update or create marks record
            Mark::updateOrCreate(
                ['student_id' => $studentId],
                [
                    'uniform' => $componentScores['uniform'],
                    'club' => $componentScores['club'],
                    'sport' => $componentScores['sport'],
                    'competition' => $componentScores['competition'],
                    'extra' => $componentScores['extra'],
                    'total' => $totalScore,
                    'grade' => $grade,
                    'is_manual_override' => false,
                    'last_updated_by' => Auth::user()->teacher->id,
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('Failed to update student marks: ' . $e->getMessage());
            throw $e;
        }
    }

    // Calculate points for a single activity.
    private function calculateActivityPoints($activity)
    {
        // Official Malaysian PAJSK Rubric for Achievement (Pencapaian)
        $rubric = [
            'international' => [
                'first' => 20,
                'second' => 19,
                'third' => 18,
                'participation' => 15
            ],
            'national' => [
                'first' => 17,
                'second' => 16,
                'third' => 15,
                'participation' => 12
            ],
            'state' => [
                'first' => 14,
                'second' => 13,
                'third' => 12,
                'participation' => 10
            ],
            'district' => [
                'first' => 11,
                'second' => 10,
                'third' => 9,
                'participation' => 8
            ],
            'school' => [
                'first' => 8,
                'second' => 7,
                'third' => 6,
                'participation' => 5
            ]
        ];

        $level = $activity->level ?? 'school';
        $achievement = $activity->achievement ?? 'participation';

        return $rubric[$level][$achievement] ?? 5; // Default to minimum if not found
    }

    // Calculate grade from total score.
    private function calculateGrade($totalScore)
    {
        if ($totalScore >= 80) return 'A';
        if ($totalScore >= 60) return 'B';
        if ($totalScore >= 40) return 'C';
        if ($totalScore >= 20) return 'D';
        return 'E';
    }

    // Get activity type text.
    private function getActivityTypeText($type)
    {
        $types = [
            'uniform' => 'Uniform Body',
            'club' => 'Club & Society',
            'sport' => 'Sports & Games',
            'competition' => 'Competition',
            'extra' => 'Extra Curriculum'
        ];
        
        return $types[$type] ?? $type;
    }
}