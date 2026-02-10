<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Report Card - {{ $student->admission_number }}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: DejaVu Sans, Arial, sans-serif;
                font-size: 11px;
                color: #1f2937;
                padding: 20px;
            }
            .header {
                text-align: center;
                border: 3px solid #3b82f6;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            }
            .logo {
                width: 80px;
                height: 80px;
                margin: 0 auto 10px;
            }
            .school-name {
                font-size: 22px;
                font-weight: 700;
                color: #1e40af;
                margin-bottom: 4px;
            }
            .school-info {
                font-size: 10px;
                color: #6b7280;
                margin-bottom: 8px;
            }
            .report-title {
                font-size: 16px;
                font-weight: 700;
                color: #1f2937;
                margin-top: 8px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .session-info {
                font-size: 11px;
                color: #3b82f6;
                font-weight: 600;
                margin-top: 4px;
            }
            .student-info {
                background: #f9fafb;
                border: 2px solid #e5e7eb;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 16px;
            }
            .info-row {
                display: table;
                width: 100%;
                margin-bottom: 6px;
            }
            .info-label {
                display: table-cell;
                width: 35%;
                color: #6b7280;
                font-weight: 600;
                font-size: 10px;
            }
            .info-value {
                display: table-cell;
                font-weight: 700;
                color: #1f2937;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 16px;
            }
            th {
                background: #3b82f6;
                color: white;
                padding: 10px 8px;
                text-align: left;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            td {
                padding: 8px;
                border-bottom: 1px solid #e5e7eb;
            }
            tr:nth-child(even) {
                background: #f9fafb;
            }
            .summary-box {
                background: #dbeafe;
                border: 2px solid #3b82f6;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 16px;
            }
            .summary-grid {
                display: table;
                width: 100%;
            }
            .summary-item {
                display: table-cell;
                text-align: center;
                padding: 8px;
            }
            .summary-label {
                font-size: 9px;
                color: #1e40af;
                font-weight: 600;
                text-transform: uppercase;
                margin-bottom: 4px;
            }
            .summary-value {
                font-size: 16px;
                font-weight: 700;
                color: #1f2937;
            }
            .footer {
                margin-top: 30px;
                padding-top: 12px;
                border-top: 2px solid #e5e7eb;
                text-align: center;
                font-size: 9px;
                color: #9ca3af;
            }
            .signature-section {
                margin-top: 30px;
                display: table;
                width: 100%;
            }
            .signature {
                display: table-cell;
                width: 50%;
                text-align: center;
                padding: 10px;
            }
            .signature-line {
                border-top: 1px solid #1f2937;
                margin-top: 40px;
                padding-top: 6px;
                font-size: 10px;
                font-weight: 600;
            }
            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 0;
                opacity: 0.08;
                width: 500px;
                height: 500px;
            }
            .content {
                position: relative;
                z-index: 1;
            }
            .header-table {
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
        </style>
    </head>
    <body>
        @if(config('myacademy.school_logo'))
            <div class="watermark">
                <img src="{{ storage_path('app/public/' . config('myacademy.school_logo')) }}" alt="Watermark" style="width: 100%; height: 100%; object-fit: contain;" />
            </div>
        @endif
        
        <div class="content">
        <div class="header">
            <div class="header-table">
                @if(config('myacademy.school_logo'))
                    <div class="header-logo">
                        <img src="{{ storage_path('app/public/' . config('myacademy.school_logo')) }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;" />
                    </div>
                @endif
                <div class="header-center">
                    <div class="school-name">{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}</div>
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
                    <div class="session-info">{{ $session }} Academic Session • Term {{ $term }}</div>
                </div>
                @if(config('myacademy.school_logo'))
                    <div class="header-logo">
                        <img src="{{ storage_path('app/public/' . config('myacademy.school_logo')) }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;" />
                    </div>
                @endif
            </div>
        </div>

        <div class="student-info">
            <div class="info-row">
                <span class="info-label">Student Name:</span>
                <span class="info-value">{{ $student->full_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Admission Number:</span>
                <span class="info-value">{{ $student->admission_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Class / Section:</span>
                <span class="info-value">{{ $student->schoolClass?->name }} / {{ $student->section?->name }}</span>
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total Score</div>
                    <div class="summary-value">{{ $grandTotal }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Average</div>
                    <div class="summary-value">{{ number_format($average, 1) }}%</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Position</div>
                    <div class="summary-value">{{ $position }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Class Average</div>
                    <div class="summary-value">{{ number_format($classAverage, 1) }}%</div>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Subject</th>
                    <th style="width: 12%; text-align: center;">CA1</th>
                    <th style="width: 12%; text-align: center;">CA2</th>
                    <th style="width: 12%; text-align: center;">Exam</th>
                    <th style="width: 12%; text-align: center;">Total</th>
                    <th style="width: 12%; text-align: center;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td><strong>{{ $row['subject']->name }}</strong></td>
                        <td style="text-align: center;">{{ $row['ca1'] ?? '—' }}</td>
                        <td style="text-align: center;">{{ $row['ca2'] ?? '—' }}</td>
                        <td style="text-align: center;">{{ $row['exam'] ?? '—' }}</td>
                        <td style="text-align: center;"><strong>{{ $row['total'] ?? '—' }}</strong></td>
                        <td style="text-align: center;"><strong>{{ $row['grade'] ?? '—' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line">Class Teacher</div>
            </div>
            <div class="signature">
                <div class="signature-line">Principal</div>
            </div>
        </div>

        <div class="footer">
            Generated on {{ now()->format('F j, Y \a\t g:i A') }} • {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
        </div>
        </div>
    </body>
</html>
