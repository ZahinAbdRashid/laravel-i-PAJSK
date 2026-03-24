<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PAJSK Score Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        .header-table { width: 100%; border: none; margin-bottom: 20px; text-align: center; }
        .header-table td { border: none; padding: 0; }
        .school-logo { width: 80px; height: auto; display: block; margin: 0 auto 10px auto; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 6px; text-transform: uppercase; color: #1e3a8a; }
        .subtitle { font-size: 14px; font-weight: bold; color: #4b5563; }
        .section-title { font-size: 14px; font-weight: bold; margin: 16px 0 8px; border-bottom: 2px solid #e5e7eb; padding-bottom: 4px; color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: 600; font-size: 11px; }
        .small { font-size: 11px; color: #6b7280; }
        .badge { padding: 2px 6px; border-radius: 999px; font-size: 10px; display: inline-block; }
        .badge-approved { background-color: #dcfce7; color: #166534; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
@php
    $totalScore = $scoreData['total_score'] ?? 0;
    $grade = $scoreData['grade'] ?? '-';
    $componentScores = $scoreData['component_scores'] ?? [];
    
    // Read the image and convert to base64 so dompdf can embed it regardless of storage logic
    $logoPath = public_path('images/logo sekolah.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;
    }
@endphp
<body>
    <table class="header-table">
        <tr>
            <td>
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="School Logo" class="school-logo">
                @endif
                <div class="title">SMK Dato' Haji Talib Karim</div>
                <div class="subtitle">PAJSK Score Report (i-PAJSK)</div>
                <div class="small" style="margin-top:8px;">Generated on: {{ now()->format('d F Y, H:i') }}</div>
            </td>
        </tr>
    </table>

    <div>
        <div class="section-title">Student Information</div>
        <table>
            <tr>
                <th style="width: 30%;">Name</th>
                <td>{{ $user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>IC Number</th>
                <td>{{ $user->ic_number ?? '-' }}</td>
            </tr>
            <tr>
                <th>Class</th>
                <td>{{ $student->teacher->assigned_class ?? '-' }}</td>
            </tr>
            <tr>
                <th>Academic Session / Semester</th>
                <td>{{ $student->academic_session ?? '-' }} / {{ $student->semester ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div>
        <div class="section-title">Current PAJSK Score Summary</div>
        <table>
            <tr>
                <th style="width: 30%;">Total Score</th>
                <td>{{ $totalScore }} / 100</td>
            </tr>
            <tr>
                <th>Grade</th>
                <td>{{ $grade }}</td>
            </tr>
        </table>

        <div class="section-title" style="margin-top:12px;">Scores by Component</div>
        <table>
            <thead>
                <tr>
                    <th>Component</th>
                    <th>Score</th>
                    <th>Maximum</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Uniform Body</td>
                    <td>{{ $componentScores['uniform'] ?? 0 }}</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>Club &amp; Society</td>
                    <td>{{ $componentScores['club'] ?? 0 }}</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>Sports &amp; Games</td>
                    <td>{{ $componentScores['sport'] ?? 0 }}</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>Competition</td>
                    <td>{{ $componentScores['competition'] ?? 0 }}</td>
                    <td>40</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <div class="section-title">Approved Activities (Evidence)</div>
        @if($activities->count() === 0)
            <p class="small">No approved activities yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:4%;">No.</th>
                        <th style="width:30%;">Activity Name</th>
                        <th style="width:14%;">Type</th>
                        <th style="width:14%;">Level</th>
                        <th style="width:14%;">Achievement</th>
                        <th style="width:12%;">Activity Date</th>
                        <th style="width:12%;">Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $index => $activity)
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

                            $points = method_exists($activity, 'calculatePoints') ? $activity->calculatePoints() : 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $activity->name }}</td>
                            <td>{{ $typeText }}</td>
                            <td>{{ $levelText }}</td>
                            <td>{{ $achievementText }}</td>
                            <td>{{ $activity->activity_date ? $activity->activity_date->format('d/m/Y') : '-' }}</td>
                            <td>{{ number_format($points, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>

