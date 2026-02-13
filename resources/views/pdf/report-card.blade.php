<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Report Card - {{ $student->admission_number }}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 10px;
                color: #1f2937;
                padding: 15px;
                background: #ffffff;
            }
            .page-border {
                border: 4px double #1e40af;
                padding: 15px;
                min-height: 100%;
            }
            .header {
                text-align: center;
                border-bottom: 3px solid #3b82f6;
                padding-bottom: 15px;
                margin-bottom: 15px;
                position: relative;
            }
            .header-flex {
                display: table;
                width: 100%;
            }
            .header-logo {
                display: table-cell;
                width: 90px;
                vertical-align: middle;
            }
            .header-center {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
            }
            .logo {
                width: 75px;
                height: 75px;
                object-fit: contain;
                border: 2px solid #3b82f6;
                border-radius: 50%;
                padding: 5px;
                background: white;
            }
            .school-name {
                font-size: 24px;
                font-weight: 700;
                color: #1e40af;
                margin-bottom: 3px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .school-motto {
                font-size: 9px;
                color: #6b7280;
                font-style: italic;
                margin-bottom: 5px;
            }
            .school-info {
                font-size: 9px;
                color: #4b5563;
                margin-bottom: 3px;
            }
            .report-title {
                font-size: 18px;
                font-weight: 700;
                color: white;
                background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
                padding: 8px;
                margin-top: 10px;
                text-transform: uppercase;
                letter-spacing: 2px;
                border-radius: 4px;
            }
            .session-bar {
                background: #dbeafe;
                padding: 8px;
                margin-bottom: 15px;
                border-left: 4px solid #3b82f6;
                display: table;
                width: 100%;
            }
            .session-item {
                display: table-cell;
                width: 33.33%;
                font-size: 9px;
                padding: 0 10px;
            }
            .session-label {
                color: #6b7280;
                font-weight: 600;
                display: block;
                margin-bottom: 2px;
            }
            .session-value {
                color: #1e40af;
                font-weight: 700;
                font-size: 10px;
            }
            .student-section {
                background: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 12px;
            }
            .student-grid {
                display: table;
                width: 100%;
            }
            .student-col {
                display: table-cell;
                width: 50%;
                padding: 5px;
            }
            .student-row {
                margin-bottom: 6px;
                border-bottom: 1px dotted #d1d5db;
                padding-bottom: 4px;
            }
            .student-label {
                color: #6b7280;
                font-weight: 600;
                font-size: 9px;
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
                width: 90px;
                height: 110px;
                border: 2px solid #3b82f6;
                border-radius: 4px;
                object-fit: cover;
            }
            .summary-cards {
                display: table;
                width: 100%;
                margin-bottom: 12px;
            }
            .summary-card {
                display: table-cell;
                width: 25%;
                padding: 8px;
            }
            .summary-inner {
                background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
                border: 2px solid #3b82f6;
                border-radius: 6px;
                padding: 10px;
                text-align: center;
            }
            .summary-label {
                font-size: 8px;
                color: #1e40af;
                font-weight: 700;
                text-transform: uppercase;
                margin-bottom: 4px;
            }
            .summary-value {
                font-size: 18px;
                font-weight: 700;
                color: #1f2937;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 12px;
                border: 2px solid #3b82f6;
            }
            th {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                color: white;
                padding: 8px 6px;
                text-align: center;
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                border: 1px solid #1e40af;
            }
            td {
                padding: 6px;
                border: 1px solid #d1d5db;
                text-align: center;
                font-size: 9px;
            }
            tr:nth-child(even) {
                background: #f9fafb;
            }
            .subject-name {
                text-align: left;
                font-weight: 700;
                color: #1f2937;
            }
            .grading-key {
                background: #fef3c7;
                border: 2px solid #f59e0b;
                border-radius: 6px;
                padding: 10px;
                margin-bottom: 12px;
            }
            .grading-title {
                font-size: 10px;
                font-weight: 700;
                color: #92400e;
                margin-bottom: 6px;
                text-align: center;
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
            }
            .grade-letter {
                font-weight: 700;
                color: #92400e;
            }
            .remarks-section {
                background: #f0fdf4;
                border: 2px solid #10b981;
                border-radius: 6px;
                padding: 10px;
                margin-bottom: 12px;
            }
            .remarks-title {
                font-size: 10px;
                font-weight: 700;
                color: #065f46;
                margin-bottom: 6px;
            }
            .remarks-content {
                min-height: 40px;
                border-bottom: 1px solid #d1d5db;
                padding: 5px;
                font-size: 9px;
                color: #1f2937;
            }
            .attendance-box {
                background: #fef2f2;
                border: 2px solid #ef4444;
                border-radius: 6px;
                padding: 10px;
                margin-bottom: 12px;
            }
            .attendance-grid {
                display: table;
                width: 100%;
            }
            .attendance-item {
                display: table-cell;
                width: 33.33%;
                text-align: center;
                padding: 5px;
            }
            .attendance-label {
                font-size: 8px;
                color: #991b1b;
                font-weight: 600;
                margin-bottom: 3px;
            }
            .attendance-value {
                font-size: 14px;
                font-weight: 700;
                color: #1f2937;
            }
            .signature-section {
                margin-top: 20px;
                display: table;
                width: 100%;
            }
            .signature {
                display: table-cell;
                width: 33.33%;
                text-align: center;
                padding: 10px;
            }
            .signature-line {
                border-top: 2px solid #1f2937;
                margin-top: 35px;
                padding-top: 5px;
                font-size: 9px;
                font-weight: 700;
            }
            .signature-label {
                font-size: 8px;
                color: #6b7280;
                margin-top: 2px;
            }
            .footer {
                margin-top: 15px;
                padding-top: 10px;
                border-top: 3px solid #3b82f6;
                text-align: center;
                font-size: 8px;
                color: #6b7280;
            }
            .next-term {
                background: #fef3c7;
                border: 2px dashed #f59e0b;
                padding: 8px;
                text-align: center;
                font-size: 9px;
                font-weight: 700;
                color: #92400e;
                margin-bottom: 10px;
            }
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: -1;
                opacity: 0.05;
                width: 400px;
                height: 400px;
            }
        </style>
    </head>
    <body>
        @if(config('myacademy.school_logo'))
            <div class="watermark">
                <img src="{{ storage_path('app/public/' . config('myacademy.school_logo')) }}" alt="Watermark" style="width: 100%; height: 100%; object-fit: contain;" />
            </div>
        @endif
        
        <div class="page-border">
            <div class="header">
                <div class="header-flex">
                    @if(config('myacademy.school_logo'))
                        <div class="header-logo">
                            <img src="{{ storage_path('app/public/' . config('myacademy.school_logo')) }}" alt="Logo" class="logo" />
                        </div>
                    @endif
                    <div class="header-center">
                        <div class="school-name">{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}</div>
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
                    @if(config('myacademy.school_logo'))
                        <div class="header-logo">
                            <img src="{{ storage_path('app/public/' . config('myacademy.school_logo')) }}" alt="Logo" class="logo" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="session-bar">
                <div class="session-item">
                    <span class="session-label">Academic Session:</span>
                    <span class="session-value">{{ $session }}</span>
                </div>
                <div class="session-item">
                    <span class="session-label">Term:</span>
                    <span class="session-value">Term {{ $term }}</span>
                </div>
                <div class="session-item">
                    <span class="session-label">Report Date:</span>
                    <span class="session-value">{{ now()->format('d M, Y') }}</span>
                </div>
            </div>

            <div class="student-section">
                <div class="student-grid">
                    <div class="student-col">
                        <div class="student-row">
                            <span class="student-label">Student Name:</span>
                            <span class="student-value">{{ $student->full_name }}</span>
                        </div>
                        <div class="student-row">
                            <span class="student-label">Admission No:</span>
                            <span class="student-value">{{ $student->admission_number }}</span>
                        </div>
                        <div class="student-row">
                            <span class="student-label">Class:</span>
                            <span class="student-value">{{ $student->schoolClass?->name }}</span>
                        </div>
                        <div class="student-row">
                            <span class="student-label">Section:</span>
                            <span class="student-value">{{ $student->section?->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="student-col">
                        <div class="student-row">
                            <span class="student-label">Gender:</span>
                            <span class="student-value">{{ $student->gender ?? 'N/A' }}</span>
                        </div>
                        <div class="student-row">
                            <span class="student-label">Date of Birth:</span>
                            <span class="student-value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M, Y') : 'N/A' }}</span>
                        </div>
                        <div class="student-row">
                            <span class="student-label">Age:</span>
                            <span class="student-value">{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->age . ' years' : 'N/A' }}</span>
                        </div>
                        <div class="student-row">
                            <span class="student-label">No. in Class:</span>
                            <span class="student-value">{{ $totalStudents ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @if($student->passport_photo)
                        <div class="passport-box">
                            <img src="{{ storage_path('app/public/' . $student->passport_photo) }}" alt="Passport" class="passport" />
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
                    <div class="summary-inner">
                        <div class="summary-label">Average</div>
                        <div class="summary-value">{{ number_format($average, 1) }}%</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-inner">
                        <div class="summary-label">Position</div>
                        <div class="summary-value">{{ $position }}</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-inner">
                        <div class="summary-label">Class Avg</div>
                        <div class="summary-value">{{ number_format($classAverage, 1) }}%</div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 30%;">Subject</th>
                        <th style="width: 10%;">CA1<br/>(20)</th>
                        <th style="width: 10%;">CA2<br/>(20)</th>
                        <th style="width: 10%;">Exam<br/>(60)</th>
                        <th style="width: 10%;">Total<br/>(100)</th>
                        <th style="width: 10%;">Grade</th>
                        <th style="width: 10%;">Class<br/>Avg</th>
                        <th style="width: 10%;">Position</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="subject-name">{{ $row['subject']->name }}</td>
                            <td>{{ $row['ca1'] ?? '—' }}</td>
                            <td>{{ $row['ca2'] ?? '—' }}</td>
                            <td>{{ $row['exam'] ?? '—' }}</td>
                            <td><strong>{{ $row['total'] ?? '—' }}</strong></td>
                            <td><strong>{{ $row['grade'] ?? '—' }}</strong></td>
                            <td>{{ $row['class_avg'] ?? '—' }}</td>
                            <td>{{ $row['position'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="grading-key">
                <div class="grading-title">GRADING SYSTEM</div>
                <div class="grading-grid">
                    <div class="grading-item">
                        <span class="grade-letter">A:</span> 70-100 (Excellent)
                    </div>
                    <div class="grading-item">
                        <span class="grade-letter">B:</span> 60-69 (Very Good)
                    </div>
                    <div class="grading-item">
                        <span class="grade-letter">C:</span> 50-59 (Good)
                    </div>
                    <div class="grading-item">
                        <span class="grade-letter">D:</span> 40-49 (Pass)
                    </div>
                    <div class="grading-item">
                        <span class="grade-letter">F:</span> 0-39 (Fail)
                    </div>
                </div>
            </div>

            <div class="attendance-box">
                <div class="attendance-grid">
                    <div class="attendance-item">
                        <div class="attendance-label">TIMES SCHOOL OPENED</div>
                        <div class="attendance-value">{{ $timesOpened ?? '—' }}</div>
                    </div>
                    <div class="attendance-item">
                        <div class="attendance-label">TIMES PRESENT</div>
                        <div class="attendance-value">{{ $timesPresent ?? '—' }}</div>
                    </div>
                    <div class="attendance-item">
                        <div class="attendance-label">TIMES ABSENT</div>
                        <div class="attendance-value">{{ $timesAbsent ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="remarks-section">
                <div class="remarks-title">CLASS TEACHER'S REMARKS:</div>
                <div class="remarks-content">{{ $teacherRemarks ?? 'No remarks provided.' }}</div>
            </div>

            <div class="remarks-section" style="background: #ede9fe; border-color: #8b5cf6;">
                <div class="remarks-title" style="color: #5b21b6;">PRINCIPAL'S REMARKS:</div>
                <div class="remarks-content">{{ $principalRemarks ?? 'No remarks provided.' }}</div>
            </div>

            <div class="next-term">
                NEXT TERM BEGINS: {{ $nextTermDate ?? 'To be announced' }}
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
                Generated on {{ now()->format('l, F j, Y \a\t g:i A') }} • {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }} • Powered by MyAcademy SMS
            </div>
        </div>
    </body>
</html>
