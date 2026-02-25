<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Report Card - {{ $student->admission_number }}</title>
    <style>
        @page {
            margin: 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            background: #fff;
        }

        .page-border {
            border: 3px solid #d97706;
            padding: 18px;
            background: white;
            position: relative;
        }

        .page-border::before {
            content: '';
            position: absolute;
            inset: 4px;
            border: 1px solid #fcd34d;
            pointer-events: none;
        }

        /* ─── Header ─── */
        .header {
            text-align: center;
            border-bottom: 3px solid #d97706;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        .header-flex {
            display: table;
            width: 100%;
        }

        .header-logo {
            display: table-cell;
            width: 90px;
            vertical-align: middle;
            text-align: center;
        }

        .header-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border: 2px solid #d97706;
            border-radius: 50%;
            padding: 4px;
            background: white;
        }

        .school-name {
            font-size: 22px;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }

        .school-motto {
            font-size: 10px;
            color: #b45309;
            font-style: italic;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .school-info {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .report-title {
            display: inline-block;
            font-size: 13px;
            font-weight: 800;
            color: white;
            background: linear-gradient(135deg, #d97706, #b45309);
            padding: 7px 24px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 3px;
            border-radius: 20px;
        }

        /* ─── Session Bar ─── */
        .session-bar {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            display: table;
            width: 100%;
        }

        .session-item {
            display: table-cell;
            width: 33.33%;
            font-size: 9px;
            padding: 0 8px;
        }

        .session-label {
            color: #92400e;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8px;
            display: block;
            margin-bottom: 2px;
        }

        .session-value {
            color: #78350f;
            font-weight: 800;
            font-size: 11px;
        }

        /* ─── Student Info ─── */
        .student-section {
            border: 2px solid #d97706;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            background: #fffbeb;
        }

        .student-grid {
            display: table;
            width: 100%;
        }

        .student-col {
            display: table-cell;
            width: 42%;
            padding: 3px 5px;
            vertical-align: top;
        }

        .student-row {
            margin-bottom: 5px;
            border-bottom: 1px dotted #fbbf24;
            padding-bottom: 4px;
        }

        .student-label {
            color: #92400e;
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            display: inline-block;
            width: 45%;
        }

        .student-value {
            font-weight: 700;
            color: #1f2937;
            font-size: 10px;
        }

        .passport-box {
            display: table-cell;
            width: 100px;
            text-align: center;
            vertical-align: top;
        }

        .passport {
            width: 85px;
            height: 100px;
            border: 2px solid #d97706;
            border-radius: 6px;
            object-fit: cover;
        }

        /* ─── Summary Cards ─── */
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 4px;
        }

        .summary-inner {
            background: linear-gradient(135deg, #d97706, #b45309);
            border-radius: 8px;
            padding: 10px 6px;
            text-align: center;
        }

        .summary-inner.green {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .summary-inner.blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }

        .summary-inner.purple {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
        }

        .summary-label {
            font-size: 7px;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
            opacity: 0.9;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 800;
            color: white;
        }

        /* ─── Scores Table ─── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 2px solid #d97706;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background: linear-gradient(135deg, #d97706, #b45309);
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #b45309;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background: #fffbeb;
        }

        .subject-name {
            text-align: left;
            font-weight: 700;
            color: #78350f;
        }

        .grade-a {
            color: #059669;
            font-weight: 800;
        }

        .grade-b {
            color: #2563eb;
            font-weight: 800;
        }

        .grade-c {
            color: #d97706;
            font-weight: 800;
        }

        .grade-d {
            color: #ea580c;
            font-weight: 800;
        }

        .grade-f {
            color: #dc2626;
            font-weight: 800;
        }

        /* ─── Grading Key ─── */
        .grading-key {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 12px;
        }

        .grading-title {
            font-size: 9px;
            font-weight: 800;
            color: #92400e;
            margin-bottom: 6px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .grading-grid {
            display: table;
            width: 100%;
        }

        .grading-item {
            display: table-cell;
            text-align: center;
            font-size: 8px;
            padding: 4px;
            font-weight: 600;
            color: #78350f;
        }

        .grade-letter {
            font-weight: 800;
            font-size: 10px;
        }

        /* ─── Attendance ─── */
        .attendance-box {
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 12px;
            background: #fffbeb;
        }

        .attendance-title {
            font-size: 9px;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            text-align: center;
        }

        .attendance-grid {
            display: table;
            width: 100%;
        }

        .attendance-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 6px 4px;
        }

        .attendance-inner {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 4px;
        }

        .attendance-label {
            font-size: 7px;
            color: #92400e;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .attendance-value {
            font-size: 16px;
            font-weight: 800;
            color: #1f2937;
        }

        /* ─── Remarks ─── */
        .remarks-section {
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
            background: #fffbeb;
        }

        .remarks-title {
            font-size: 9px;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .remarks-content {
            min-height: 30px;
            border-bottom: 1px solid #d97706;
            padding: 4px 0;
            font-size: 9px;
            color: #374151;
        }

        /* ─── Next Term ─── */
        .next-term {
            background: linear-gradient(135deg, #d97706, #b45309);
            padding: 8px;
            text-align: center;
            font-size: 10px;
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ─── Signatures ─── */
        .signature-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }

        .signature {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 8px;
        }

        .signature-line {
            border-top: 2px solid #78350f;
            margin-top: 35px;
            padding-top: 5px;
            font-size: 9px;
            font-weight: 800;
            color: #78350f;
        }

        .signature-label {
            font-size: 7px;
            color: #9ca3af;
            margin-top: 2px;
            font-style: italic;
        }

        /* ─── Footer ─── */
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 2px solid #d97706;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
        }

        /* ─── Watermark ─── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            opacity: 0.03;
            width: 400px;
            height: 400px;
        }
    </style>
</head>

<body>
    @php
        $schoolName = config('myacademy.school_name', config('app.name', 'MyAcademy'));
        $logo = config('myacademy.school_logo');
        $logoPath = $logo ? public_path('uploads/' . str_replace('\\', '/', $logo)) : null;
        $logoExists = $logoPath && file_exists($logoPath);
    @endphp

    @if($logoExists)
        <div class="watermark">
            <img src="{{ $logoPath }}" alt="" style="width: 100%; height: 100%; object-fit: contain;" />
        </div>
    @endif

    <div class="page-border">
        <div class="header">
            <div class="header-flex">
                @if($logoExists)
                    <div class="header-logo">
                        <img src="{{ $logoPath }}" alt="Logo" class="logo" />
                    </div>
                @endif
                <div class="header-center">
                    <div class="school-name">{{ $schoolName }}</div>
                    @if(config('myacademy.school_motto'))
                        <div class="school-motto">"{{ config('myacademy.school_motto') }}"</div>
                    @endif
                    @if(config('myacademy.school_address'))
                        <div class="school-info">{{ config('myacademy.school_address') }}</div>
                    @endif
                    @if(config('myacademy.school_phone') || config('myacademy.school_email'))
                        <div class="school-info">
                            {{ config('myacademy.school_phone') }}
                            @if(config('myacademy.school_phone') && config('myacademy.school_email')) • @endif
                            {{ config('myacademy.school_email') }}
                        </div>
                    @endif
                    <div class="report-title">Student Report Card</div>
                </div>
                @if($logoExists)
                    <div class="header-logo">
                        <img src="{{ $logoPath }}" alt="Logo" class="logo" />
                    </div>
                @endif
            </div>
        </div>

        <div class="session-bar">
            <div class="session-item">
                <span class="session-label">Academic Session</span>
                <span class="session-value">{{ $session }}</span>
            </div>
            <div class="session-item">
                <span class="session-label">Term</span>
                <span class="session-value">Term {{ $term }}</span>
            </div>
            <div class="session-item">
                <span class="session-label">Report Date</span>
                <span class="session-value">{{ now()->format('d M, Y') }}</span>
            </div>
        </div>

        <div class="student-section">
            <div class="student-grid">
                <div class="student-col">
                    <div class="student-row">
                        <span class="student-label">Student Name</span>
                        <span class="student-value">{{ $student->full_name }}</span>
                    </div>
                    <div class="student-row">
                        <span class="student-label">Admission No</span>
                        <span class="student-value">{{ $student->admission_number }}</span>
                    </div>
                    <div class="student-row">
                        <span class="student-label">Class</span>
                        <span class="student-value">{{ $student->schoolClass?->name }}</span>
                    </div>
                    <div class="student-row">
                        <span class="student-label">Section</span>
                        <span class="student-value">{{ $student->section?->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="student-col">
                    <div class="student-row">
                        <span class="student-label">Gender</span>
                        <span class="student-value">{{ $student->gender ?? 'N/A' }}</span>
                    </div>
                    <div class="student-row">
                        <span class="student-label">Date of Birth</span>
                        <span
                            class="student-value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M, Y') : 'N/A' }}</span>
                    </div>
                    <div class="student-row">
                        <span class="student-label">Age</span>
                        <span
                            class="student-value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->age . ' years' : 'N/A' }}</span>
                    </div>
                    <div class="student-row">
                        <span class="student-label">No. in Class</span>
                        <span class="student-value">{{ $totalStudents ?? 'N/A' }}</span>
                    </div>
                </div>
                @if($student->passport_photo)
                    <div class="passport-box">
                        <img src="{{ public_path('uploads/' . str_replace('\\', '/', $student->passport_photo)) }}"
                            alt="Photo" class="passport" />
                    </div>
                @endif
            </div>
        </div>

        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-inner">
                    <div class="summary-label">Total Score</div>
                    <div class="summary-value">{{ $grandTotal }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-inner green">
                    <div class="summary-label">Average</div>
                    <div class="summary-value">{{ number_format($average, 1) }}%</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-inner blue">
                    <div class="summary-label">Position</div>
                    <div class="summary-value">{{ $position }}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-inner purple">
                    <div class="summary-label">Class Avg</div>
                    <div class="summary-value">{{ number_format($classAverage, 1) }}%</div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 30%; text-align: left; padding-left: 8px;">Subject</th>
                    <th style="width: 10%;">CA1<br />({{ config('myacademy.results_ca1_max', 20) }})</th>
                    <th style="width: 10%;">CA2<br />({{ config('myacademy.results_ca2_max', 20) }})</th>
                    <th style="width: 10%;">Exam<br />({{ config('myacademy.results_exam_max', 60) }})</th>
                    <th style="width: 10%;">Total<br />(100)</th>
                    <th style="width: 10%;">Grade</th>
                    <th style="width: 10%;">Class<br />Avg</th>
                    <th style="width: 10%;">Pos.</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    @php
                        $grade = $row['grade'] ?? '';
                        $gradeClass = match (strtoupper($grade)) {
                            'A' => 'grade-a',
                            'B' => 'grade-b',
                            'C' => 'grade-c',
                            'D' => 'grade-d',
                            'F' => 'grade-f',
                            default => '',
                        };
                    @endphp
                    <tr>
                        <td class="subject-name">{{ $row['subject']->name }}</td>
                        <td>{{ $row['ca1'] ?? '—' }}</td>
                        <td>{{ $row['ca2'] ?? '—' }}</td>
                        <td>{{ $row['exam'] ?? '—' }}</td>
                        <td><strong>{{ $row['total'] ?? '—' }}</strong></td>
                        <td class="{{ $gradeClass }}">{{ $grade ?: '—' }}</td>
                        <td>{{ $row['class_avg'] ?? '—' }}</td>
                        <td>{{ $row['position'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grading-key">
            <div class="grading-title">Grading System</div>
            <div class="grading-grid">
                <div class="grading-item"><span class="grade-letter grade-a">A:</span> 70-100 (Excellent)</div>
                <div class="grading-item"><span class="grade-letter grade-b">B:</span> 60-69 (Very Good)</div>
                <div class="grading-item"><span class="grade-letter grade-c">C:</span> 50-59 (Good)</div>
                <div class="grading-item"><span class="grade-letter grade-d">D:</span> 40-49 (Pass)</div>
                <div class="grading-item"><span class="grade-letter grade-f">F:</span> 0-39 (Fail)</div>
            </div>
        </div>

        <div class="attendance-box">
            <div class="attendance-title">Attendance Record</div>
            <div class="attendance-grid">
                <div class="attendance-item">
                    <div class="attendance-inner">
                        <div class="attendance-label">Times Opened</div>
                        <div class="attendance-value">{{ $timesOpened ?? '—' }}</div>
                    </div>
                </div>
                <div class="attendance-item">
                    <div class="attendance-inner">
                        <div class="attendance-label">Times Present</div>
                        <div class="attendance-value">{{ $timesPresent ?? '—' }}</div>
                    </div>
                </div>
                <div class="attendance-item">
                    <div class="attendance-inner">
                        <div class="attendance-label">Times Absent</div>
                        <div class="attendance-value">{{ $timesAbsent ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="remarks-section">
            <div class="remarks-title">Class Teacher's Remarks</div>
            <div class="remarks-content">{{ $teacherRemarks ?? 'No remarks provided.' }}</div>
        </div>

        <div class="remarks-section">
            <div class="remarks-title">Principal's Remarks</div>
            <div class="remarks-content">{{ $principalRemarks ?? 'No remarks provided.' }}</div>
        </div>

        <div class="next-term">
            Next Term Begins: {{ $nextTermDate ?? 'To be announced' }}
        </div>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line">Class Teacher</div>
                <div class="signature-label">Signature & Date</div>
            </div>
            <div class="signature">
                <div class="signature-line">Principal</div>
                <div class="signature-label">Signature & Stamp</div>
            </div>
            <div class="signature">
                <div class="signature-line">Parent/Guardian</div>
                <div class="signature-label">Signature & Date</div>
            </div>
        </div>

        <div class="footer">
            Generated on {{ now()->format('l, F j, Y \a\t g:i A') }} • {{ $schoolName }} • Powered by MyAcademy SMS
        </div>
    </div>
</body>

</html>