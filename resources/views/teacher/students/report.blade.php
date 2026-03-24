<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class PAJSK Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
        }
        
        .header-table td {
            vertical-align: middle;
        }
        
        .logo-cell {
            width: 80px;
        }
        
        .logo {
            width: 70px;
            height: auto;
        }
        
        .school-info h1 {
            color: #1e3a8a;
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .school-info p {
            margin: 0 0 3px 0;
            font-size: 11px;
            color: #4b5563;
        }
        
        .report-title-cell {
            text-align: right;
            vertical-align: bottom;
        }
        
        .report-title-cell h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #111827;
        }
        
        /* Information section */
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 3px 0;
        }
        
        .label {
            font-weight: bold;
            color: #4b5563;
            width: 120px;
        }
        
        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table th, table.data-table td {
            border: 1px solid #d1d5db;
            padding: 6px;
        }
        
        table.data-table th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: bold;
            text-align: left;
        }

        table.data-table th.marks, table.data-table td.marks {
            text-align: center;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ $logoBase64 }}" class="logo" alt="School Logo">
            </td>
            <td class="school-info">
                <h1>SMK Dato' Haji Talib Karim</h1>
                <p>Jalan Pegawai, 78000 Alor Gajah, Melaka</p>
                <p>Email: smkdhtk@moe.edu.my | Tel: 06-5561234</p>
            </td>
            <td class="report-title-cell">
                <h2>BAHAGIAN SEKOLAH MENENGAH</h2>
                <p>PAJSK CLASS REPORT</p>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="label">Teacher Name:</td>
            <td>{{ $teacher->user->name }}</td>
            <td class="label">Date Generated:</td>
            <td>{{ date('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Assigned Class:</td>
            <td>{{ $teacher->assigned_class ?? 'All' }}</td>
            <td class="label">Report Year:</td>
            <td>{{ date('Y') }}</td>
        </tr>
        <tr>
            <td class="label">Number of Students:</td>
            <td>{{ rtrim(rtrim(number_format($students->count(), 1, '.', ''), '0'), '.') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No.</th>
                <th width="20%">Student Name</th>
                <th width="12%">IC Number</th>
                <th class="marks" width="9%">Uniform<br>(20)</th>
                <th class="marks" width="9%">Club<br>(20)</th>
                <th class="marks" width="9%">Sport<br>(20)</th>
                <th class="marks" width="10%">Competition<br>(40)</th>
                <th class="marks" width="8%">Extra<br>(10)</th>
                <th class="marks" width="10%">Total<br>(100+)</th>
                <th class="marks" width="10%">Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->user->name ?? 'Unknown' }}</td>
                    <td>{{ $student->user->ic_number ?? '-' }}</td>
                    <td class="marks">{{ number_format($student->marks->first()->uniform ?? 0, 1) }}</td>
                    <td class="marks">{{ number_format($student->marks->first()->club ?? 0, 1) }}</td>
                    <td class="marks">{{ number_format($student->marks->first()->sport ?? 0, 1) }}</td>
                    <td class="marks">{{ number_format($student->marks->first()->competition ?? 0, 1) }}</td>
                    <td class="marks">{{ number_format($student->marks->first()->extra ?? 0, 1) }}</td>
                    <td class="marks" style="font-weight: bold;">
                        {{ number_format($student->marks->first()->total ?? 0, 1) }}
                    </td>
                    <td class="marks" style="font-weight: bold;">
                        {{ $student->marks->first()->grade ?? 'E' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document and requires no signature.</p>
        <p>Generated by i-PAJSK System on {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
