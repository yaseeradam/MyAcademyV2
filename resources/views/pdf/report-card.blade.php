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
                padding: 20px;
                background: #f8fafc;
            }
            .page-border {
                border: 5px solid #0ea5e9;
                padding: 20px;
                background: white;
                box-shadow: 0 0 0 2px #bae6fd;
            }
            .header {
                text-align: center;
                border-bottom: 4px solid #0ea5e9;
                padding-bottom: 15px;
                margin-bottom: 15px;
                background: linear-gradient(to bottom, #f0f9ff 0%, white 100%);
                padding-top: 10px;
            }
            .header-flex {
                display: table;
                width: 100%;
            }
            .header-logo {
                display: table-cell;
                width: 100px;
                vertical-align: middle;
            }
            .header-center {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
            }
            .logo {
                width: 80px;
                height: 80px;
                object-fit: contain;
                border: 3px solid #0ea5e9;
                border-radius: 50%;
                padding: 5px;
                background: white;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .school-name {
                font-size: 26px;
                font-weight: 700;
                color: #0c4a6e;
                margin-bottom: 4px;
                text-transform: uppercase;
                letter-spacing: 2px;
            }
            .school-motto {
                font-size: 10px;
                color: #0369a1;
                font-style: italic;
                margin-bottom: 6px;
                font-weight: 600;
            }
            .school-info {
                font-size: 9px;
                color: #475569;
                margin-bottom: 3px;
            }
            .report-title {
                font-size: 16px;
                font-weight: 700;
                color: white;
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
                padding: 10px;
                margin-top: 10px;
                text-transform: uppercase;
                letter-spacing: 3px;
                border-radius: 6px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .session-bar {
                background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
                padding: 10px;
                margin-bottom: 15px;
                border-left: 5px solid #0ea5e9;
                border-radius: 4px;
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
                color: #0369a1;
                font-weight: 700;
                display: block;
                margin-bottom: 3px;
                text-transform: uppercase;
                font-size: 8px;
            }
            .session-value {
                color: #0c4a6e;
                font-weight: 700;
                font-size: 11px;
            }
            .student-section {
                background: linear-gradient(to right, #fef3c7 0%, #fde68a 100%);
                border: 3px solid #f59e0b;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 15px;
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
                margin-bottom: 7px;
                border-bottom: 1px solid #fbbf24;
                padding-bottom: 5px;
            }
            .student-label {
                color: #92400e;
                font-weight: 700;
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
                width: 110px;
                text-align: center;
                vertical-align: top;
            }
            .passport {
                width: 95px;
                height: 115px;
                border: 3px solid #f59e0b;
                border-radius: 6px;
                object-fit: cover;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .summary-cards {
                display: table;
                width: 100%;
                margin-bottom: 15px;
            }
            .summary-card {
                display: table-cell;
                width: 25%;
                padding: 5px;
            }
            .summary-inner {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                border-radius: 8px;
                padding: 12px;
                text-align: center;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .summary-label {
                font-size: 8px;
                color: white;
                font-weight: 700;
                text-transform: uppercase;
                margin-bottom: 5px;
                opacity: 0.9;
            }
            .summary-value {
                font-size: 20px;
                font-weight: 700;
                color: white;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
                border: 3px solid #0ea5e9;
                border-radius: 8px;
                overflow: hidden;
            }
            th {
                background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
                color: white;
                padding: 10px 6px;
                text-align: center;
                font-size: 9px;
                font-weight: 700;
                text-transform: uppercase;
                border: 1px solid #0284c7;
            }
            td {
                padding: 8px 6px;
                border: 1px solid #cbd5e1;
                text-align: center;
                font-size: 9px;
            }
            tr:nth-child(even) {
                background: #f1f5f9;
            }
            tr:hover {
                background: #e0f2fe;
            }
            .subject-name {
                text-align: left;
                font-weight: 700;
                color: #0c4a6e;
            }
            .grading-key {
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                border: 3px solid #f59e0b;
                border-radius: 8px;
                padding: 12px;
                margin-bottom: 15px;
            }
            .grading-title {
                font-size: 11px;
                font-weight: 700;
                color: #92400e;
                margin-bottom: 8px;
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
                padding: 5px;
                font-weight: 600;
                color: #78350f;
            }
            .grade-letter {
                font-weight: 700;
                color: #92400e;
                font-size: 10px;
            }
            .remarks-section {
                background: linear-gradient(to right, #d1fae5 0%, #a7f3d0 100%);
                border: 3px solid #10b981;
                border-radius: 8px;
                padding: 12px;
                margin-bottom: 15px;
            }
            .remarks-title {
                font-size: 10px;
                font-weight: 700;
                color: #065f46;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .remarks-content {
                min-height: 45px;
                border-bottom: 2px solid #10b981;
                padding: 6px;
                font-size: 9px;
                color: #1f2937;
                background: white;
                border-radius: 4px;
            }
            .attendance-box {
                background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
                border: 3px solid #ef4444;
                border-radius: 8px;
                padding: 12px;
                margin-bottom: 15px;
            }
            .attendance-grid {
                display: table;
                width: 100%;
            }
            .attendance-item {
                display: table-cell;
                width: 33.33%;
                text-align: center;
                padding: 8px;
                background: white;
                margin: 0 5px;
                border-radius: 6px;
            }
            .attendance-label {
                font-size: 8px;
                color: #991b1b;
                font-weight: 700;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            .attendance-value {
                font-size: 16px;
                font-weight: 700;
                color: #1f2937;
            }
            .signature-section {
                margin-top: 25px;
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
                border-top: 2px solid #0c4a6e;
                margin-top: 40px;
                padding-top: 6px;
                font-size: 10px;
                font-weight: 700;
                color: #0c4a6e;
            }
            .signature-label {
                font-size: 8px;
                color: #64748b;
                margin-top: 3px;
                font-style: italic;
            }
            .footer {
                margin-top: 20px;
                padding-top: 12px;
                border-top: 4px solid #0ea5e9;
                text-align: center;
                font-size: 8px;
                color: #64748b;
                background: #f8fafc;
                padding-bottom: 8px;
                border-radius: 4px;
            }
            .next-term {
                background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                border: 3px solid #f59e0b;
                padding: 10px;
                text-align: center;
                font-size: 10px;
                font-weight: 700;
                color: #92400e;
                margin-bottom: 12px;
                border-radius: 6px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: -1;
                opacity: 0.03;
                width: 450px;
                height: 450px;
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

            <div class="remarks-section" style="background: linear-gradient(to right, #e9d5ff 0%, #d8b4fe 100%); border-color: #a855f7;">
                <div class="remarks-title" style="color: #6b21a8;">PRINCIPAL'S REMARKS:</div>
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
