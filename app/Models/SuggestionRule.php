<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Student;

class SuggestionRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'priority', 'active', 'expiry_date', 'conditions', 'actions'
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'active' => 'boolean',
        'expiry_date' => 'date'
    ];

    // Scope for active rules
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope for priority order
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    // Check if rule matches student conditions
    public function matchesStudent(Student $student, array $metrics)
    {
        foreach ($this->conditions as $condition) {
            if (!$this->checkCondition($condition, $metrics)) {
                return false;
            }
        }
        return true;
    }

    // Private method to check individual condition
    private function checkCondition(array $condition, array $metrics): bool
    {
        $type = $condition['type'] ?? null;
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? null;

        if (!$type) {
            return false;
        }

        switch ($type) {
            case 'grade':
                // Compare current grade (A–E) using an ordered scale
                $gradeOrder = ['E' => 1, 'D' => 2, 'C' => 3, 'B' => 4, 'A' => 5];
                $currentGrade = $metrics['grade'] ?? null;
                if (!$currentGrade || !isset($gradeOrder[$currentGrade])) {
                    return false;
                }

                $current = $gradeOrder[$currentGrade];
                $target = $gradeOrder[$value] ?? null;
                if ($target === null) {
                    return false;
                }

                return $this->compareNumeric($current, $operator, $target);

            case 'score':
                // Compare total PAJSK score (0–100)
                $score = $metrics['totalScore'] ?? null;
                if ($score === null) {
                    return false;
                }

                return $this->compareNumeric($score, $operator, (float) $value);

            case 'weak_component':
                // Check if a component (uniform / club / sport / competition) is the weakest
                $componentScores = $metrics['componentScores'] ?? [];
                if (!isset($componentScores[$value])) {
                    return false;
                }

                if (empty($componentScores)) {
                    return false;
                }

                $minScore = min($componentScores);
                $weakComponents = array_keys($componentScores, $minScore, true);

                return in_array($value, $weakComponents, true);

            case 'has_activity':
                // Check if student has at least one approved activity of given type
                $hasActivity = $metrics['hasActivity'][$value] ?? false;

                if ($operator === 'not_equals') {
                    return !$hasActivity;
                }

                return (bool) $hasActivity;

            case 'missing_activity':
                // Inverse of has_activity
                $hasActivity = $metrics['hasActivity'][$value] ?? false;

                if ($operator === 'not_equals') {
                    return (bool) $hasActivity;
                }

                return !$hasActivity;

            default:
                // Unknown condition type – do not match
                return false;
        }
    }

    private function compareNumeric(float $left, string $operator, float $right): bool
    {
        switch ($operator) {
            case 'greater_than':
                return $left > $right;
            case 'less_than':
                return $left < $right;
            case 'greater_or_equal':
                return $left >= $right;
            case 'less_or_equal':
                return $left <= $right;
            case 'not_equals':
                return $left != $right;
            case 'equals':
            default:
                return $left == $right;
        }
    }

    // Get all suggestions for a student
    public static function getSuggestionsForStudent(Student $student)
    {
        $metrics = self::calculateStudentMetrics($student);

        return self::active()
            ->byPriority()
            ->get()
            ->filter(function ($rule) use ($student, $metrics) {
                return $rule->matchesStudent($student, $metrics);
            })
            ->flatMap(function ($rule) {
                return $rule->actions;
            });
    }

    /**
     * Calculate total score, component scores, grade, and activity flags
     * based on approved activities, mirroring the student dashboard logic.
     */
    private static function calculateStudentMetrics(Student $student): array
    {
        $approvedActivities = $student->approvedActivities()->get();

        $totalScore = 0;
        $componentScores = [
            'uniform' => 0,
            'club' => 0,
            'sport' => 0,
            'competition' => 0,
        ];

        $hasActivity = [
            'uniform' => false,
            'club' => false,
            'sport' => false,
            'competition' => false,
        ];

        foreach ($approvedActivities as $activity) {
            $points = method_exists($activity, 'calculatePoints')
                ? $activity->calculatePoints()
                : 0;

            $totalScore += $points;

            if (isset($componentScores[$activity->type])) {
                $componentScores[$activity->type] += $points;
                $hasActivity[$activity->type] = true;
            }
        }

        // Cap scores similar to dashboard calculations
        $totalScore = min($totalScore, 100);
        $componentScores['uniform'] = min($componentScores['uniform'], 20);
        $componentScores['club'] = min($componentScores['club'], 20);
        $componentScores['sport'] = min($componentScores['sport'], 20);
        $componentScores['competition'] = min($componentScores['competition'], 40);

        // Determine grade
        if ($totalScore >= 80) {
            $grade = 'A';
        } elseif ($totalScore >= 60) {
            $grade = 'B';
        } elseif ($totalScore >= 40) {
            $grade = 'C';
        } elseif ($totalScore >= 20) {
            $grade = 'D';
        } else {
            $grade = 'E';
        }

        return [
            'totalScore' => $totalScore,
            'grade' => $grade,
            'componentScores' => $componentScores,
            'hasActivity' => $hasActivity,
        ];
    }
}