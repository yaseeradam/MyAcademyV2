<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Form - {{ $student->admission_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; line-height: 1.4; padding: 30px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #f59e0b; padding-bottom: 20px; }
        .school-name { font-size: 24pt; font-weight: bold; color: #1f2937; margin-bottom: 5px; }
        .form-title { font-size: 16pt; font-weight: bold; color: #f59e0b; margin-top: 10px; }
        .section { margin-bottom: 25px; }
        .section-title { background: #f59e0b; color: white; padding: 8px 12px; font-weight: bold; font-size: 12pt; margin-bottom: 15px; }
        .field { margin-bottom: 12px; }
        .field-label { font-weight: bold; color: #4b5563; display: inline-block; width: 180px; }
        .field-value { color: #1f2937; display: inline-block; }
        .grid { display: table; width: 100%; }
        .grid-row { display: table-row; }
        .grid-col { display: table-cell; padding: 8px 12px; vertical-align: top; }
        .grid-col-2 { width: 50%; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; }
        .signature-box { display: inline-block; width: 45%; text-align: center; margin-top: 50px; }
        .signature-line { border-top: 2px solid #1f2937; padding-top: 8px; margin-top: 60px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ config('myacademy.school_name', 'MyAcademy') }}</div>
        <div style="color: #6b7280; font-size: 10pt;">{{ config('myacademy.school_tagline', 'Excellence in Education') }}</div>
        <div class="form-title">STUDENT ADMISSION FORM</div>
    </div>

    <div class="section">
        <div class="section-title">STUDENT INFORMATION</div>
        <div class="field">
            <span class="field-label">Admission Number:</span>
            <span class="field-value">{{ $student->admission_number }}</span>
        </div>
        <div class="field">
            <span class="field-label">Full Name:</span>
            <span class="field-value">{{ $student->full_name }}</span>
        </div>
        <div class="field">
            <span class="field-label">Gender:</span>
            <span class="field-value">{{ $student->gender }}</span>
        </div>
        <div class="field">
            <span class="field-label">Date of Birth:</span>
            <span class="field-value">{{ $student->dob?->format('F j, Y') ?: '—' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Blood Group:</span>
            <span class="field-value">{{ $student->blood_group ?: '—' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">ACADEMIC INFORMATION</div>
        <div class="field">
            <span class="field-label">Class:</span>
            <span class="field-value">{{ $student->schoolClass?->name ?: '—' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Section:</span>
            <span class="field-value">{{ $student->section?->name ?: '—' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Status:</span>
            <span class="field-value">{{ $student->status }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">GUARDIAN INFORMATION</div>
        <div class="field">
            <span class="field-label">Guardian Name:</span>
            <span class="field-value">{{ $student->guardian_name ?: '—' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Guardian Phone:</span>
            <span class="field-value">{{ $student->guardian_phone ?: '—' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Guardian Address:</span>
            <span class="field-value">{{ $student->guardian_address ?: '—' }}</span>
        </div>
    </div>

    <div class="footer">
        <div class="signature-box" style="float: left;">
            <div class="signature-line">Guardian Signature</div>
        </div>
        <div class="signature-box" style="float: right;">
            <div class="signature-line">School Administrator</div>
        </div>
        <div style="clear: both; text-align: center; margin-top: 30px; color: #6b7280; font-size: 9pt;">
            Generated on {{ now()->format('F j, Y g:i A') }}
        </div>
    </div>
</body>
</html>
