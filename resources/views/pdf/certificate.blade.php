@php
    $schoolName = config('myacademy.school_name', config('app.name', 'MyAcademy'));
    $logo = config('myacademy.school_logo');
    $logoPath = $logo ? public_path('uploads/'.str_replace('\\', '/', $logo)) : null;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate</title>
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; }
        .container { border: 6px solid #0ea5e9; padding: 40px; text-align: center; }
        .header { display: flex; justify-content: center; align-items: center; gap: 20px; margin-bottom: 30px; }
        .logo { width: 80px; height: 80px; object-fit: contain; }
        .school { font-size: 28px; font-weight: bold; }
        .title { font-size: 42px; font-weight: bold; color: #0ea5e9; margin: 30px 0 10px; text-transform: uppercase; }
        .type { font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; }
        .student { font-size: 24px; font-weight: bold; margin: 20px 0; border-bottom: 2px solid #cbd5e1; padding-bottom: 10px; display: inline-block; min-width: 400px; }
        .body { font-size: 16px; line-height: 1.8; margin: 30px 60px; text-align: center; }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; align-items: flex-end; }
        .signature { text-align: center; }
        .sig-line { border-top: 1px solid #94a3b8; padding-top: 8px; margin-top: 50px; width: 200px; font-size: 12px; color: #64748b; }
        .info { text-align: right; font-size: 11px; color: #64748b; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        @if($logoPath && file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo">
        @endif
        <div class="school">{{ $schoolName }}</div>
    </div>

    <div class="title">{{ $certificate->title }}</div>
    <div class="type">{{ $certificate->type }}</div>

    <div style="margin: 40px 0;">
        <div style="font-size: 14px; color: #64748b; margin-bottom: 10px;">This is presented to</div>
        <div class="student">{{ $student?->full_name }}</div>
        <div style="font-size: 12px; color: #64748b; margin-top: 8px;">{{ $student?->admission_number }} • {{ $student?->schoolClass?->name }} {{ $student?->section?->name }}</div>
    </div>

    <div class="body">{{ $certificate->body }}</div>

    <div class="footer">
        <div class="signature">
            <div class="sig-line">Authorized Signature</div>
        </div>
        <div class="info">
            Serial: {{ $certificate->serial_number }}<br>
            Date: {{ $certificate->issued_on?->format('F j, Y') }}
        </div>
    </div>
</div>
</body>
</html>
