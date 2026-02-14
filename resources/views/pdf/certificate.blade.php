@php
    $schoolName = config('myacademy.school_name', config('app.name', 'MyAcademy'));
    $logo = config('myacademy.school_logo');
    $logoPath = $logo ? public_path('uploads/'.str_replace('\\', '/', $logo)) : null;
    
    $borderColor = config('myacademy.certificate_border_color', '#0ea5e9');
    $accentColor = config('myacademy.certificate_accent_color', '#0ea5e9');
    $showLogo = config('myacademy.certificate_show_logo', true);
    $showWatermark = config('myacademy.certificate_show_watermark', false);
    $watermark = config('myacademy.certificate_watermark_image');
    $watermarkPath = ($watermark && $showWatermark) ? public_path('uploads/'.str_replace('\\', '/', $watermark)) : null;
    
    $sig1Label = config('myacademy.certificate_signature_label', 'Authorized Signature');
    $sig1Name = config('myacademy.certificate_signature_name');
    $sig1Image = config('myacademy.certificate_signature_image');
    $sig1ImagePath = $sig1Image ? public_path('uploads/'.str_replace('\\', '/', $sig1Image)) : null;
    
    $sig2Label = config('myacademy.certificate_signature2_label');
    $sig2Name = config('myacademy.certificate_signature2_name');
    $sig2Image = config('myacademy.certificate_signature2_image');
    $sig2ImagePath = $sig2Image ? public_path('uploads/'.str_replace('\\', '/', $sig2Image)) : null;
    
    function hexToRgb($hex) {
        $hex = str_replace('#', '', $hex);
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }
    $rgb = hexToRgb($accentColor);
    $lightAccent = 'rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ', 0.08)';
    $mediumAccent = 'rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ', 0.25)';
    $darkAccent = 'rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ', 0.6)';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Achievement</title>
    <style>
        @page { 
            margin: 15px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            color: #0f172a;
            background: #ffffff;
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: 0.03;
            width: 500px;
            height: 500px;
            object-fit: contain;
            z-index: 0;
        }
        
        /* Main wrapper */
        .certificate-page {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        /* Geometric pattern background */
        .geometric-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.4;
            z-index: 0;
        }
        
        .pattern-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid {{ $mediumAccent }};
        }
        
        .pattern-tl {
            top: -80px;
            left: -80px;
            width: 200px;
            height: 200px;
        }
        
        .pattern-tr {
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
        }
        
        .pattern-bl {
            bottom: -60px;
            left: -60px;
            width: 180px;
            height: 180px;
        }
        
        .pattern-br {
            bottom: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
        }
        
        .pattern-line {
            position: absolute;
            background: {{ $lightAccent }};
        }
        
        .line-1 {
            top: 0;
            right: 25%;
            width: 2px;
            height: 100%;
            transform: rotate(15deg);
        }
        
        .line-2 {
            top: 0;
            left: 20%;
            width: 1px;
            height: 100%;
            transform: rotate(-12deg);
        }
        
        /* Premium border system */
        .border-outer {
            position: relative;
            border: 3px solid {{ $borderColor }};
            padding: 12px;
            z-index: 1;
            background: #ffffff;
        }
        
        .border-middle {
            border: 1px solid {{ $darkAccent }};
            padding: 10px;
            background: linear-gradient(135deg, #ffffff 0%, {{ $lightAccent }} 50%, #ffffff 100%);
        }
        
        .border-inner {
            border: 5px double {{ $accentColor }};
            padding: 50px 60px;
            position: relative;
            background: #ffffff;
        }
        
        /* Ornate corner flourishes */
        .corner-ornament {
            position: absolute;
            width: 80px;
            height: 80px;
            z-index: 2;
        }
        
        .ornament-tl {
            top: -2px;
            left: -2px;
        }
        
        .ornament-tr {
            top: -2px;
            right: -2px;
            transform: rotate(90deg);
        }
        
        .ornament-bl {
            bottom: -2px;
            left: -2px;
            transform: rotate(-90deg);
        }
        
        .ornament-br {
            bottom: -2px;
            right: -2px;
            transform: rotate(180deg);
        }
        
        .corner-ornament::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 30px;
            height: 3px;
            background: {{ $accentColor }};
        }
        
        .corner-ornament::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 30px;
            background: {{ $accentColor }};
        }
        
        /* Decorative dots in corners */
        .corner-dot {
            position: absolute;
            width: 8px;
            height: 8px;
            background: {{ $accentColor }};
            border-radius: 50%;
        }
        
        .dot-tl-1 { top: 18px; left: 18px; }
        .dot-tl-2 { top: 12px; left: 35px; width: 5px; height: 5px; }
        .dot-tl-3 { top: 35px; left: 12px; width: 5px; height: 5px; }
        
        .dot-tr-1 { top: 18px; right: 18px; }
        .dot-tr-2 { top: 12px; right: 35px; width: 5px; height: 5px; }
        .dot-tr-3 { top: 35px; right: 12px; width: 5px; height: 5px; }
        
        .dot-bl-1 { bottom: 18px; left: 18px; }
        .dot-bl-2 { bottom: 12px; left: 35px; width: 5px; height: 5px; }
        .dot-bl-3 { bottom: 35px; left: 12px; width: 5px; height: 5px; }
        
        .dot-br-1 { bottom: 18px; right: 18px; }
        .dot-br-2 { bottom: 12px; right: 35px; width: 5px; height: 5px; }
        .dot-br-3 { bottom: 35px; right: 12px; width: 5px; height: 5px; }
        
        /* Header section */
        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .logo-container {
            position: relative;
            display: inline-block;
            margin-bottom: 18px;
        }
        
        .logo {
            width: 95px;
            height: 95px;
            object-fit: contain;
            position: relative;
            z-index: 1;
        }
        
        .logo-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110px;
            height: 110px;
            border: 2px solid {{ $mediumAccent }};
            border-radius: 50%;
            z-index: 0;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        
        /* Elegant divider with ornament */
        .ornamental-divider {
            margin: 28px auto;
            text-align: center;
            position: relative;
            height: 20px;
        }
        
        .divider-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(to right, transparent, {{ $accentColor }} 20%, {{ $accentColor }} 80%, transparent);
        }
        
        .divider-center {
            position: relative;
            display: inline-block;
            background: #ffffff;
            padding: 0 15px;
            z-index: 1;
        }
        
        .divider-diamond {
            width: 12px;
            height: 12px;
            background: {{ $accentColor }};
            transform: rotate(45deg);
            display: inline-block;
        }
        
        /* Certificate title with ribbon effect */
        .title-section {
            text-align: center;
            margin: 35px 0 25px;
            position: relative;
        }
        
        .certificate-title {
            font-size: 52px;
            font-weight: bold;
            color: {{ $accentColor }};
            text-transform: uppercase;
            letter-spacing: 6px;
            margin: 0;
            line-height: 1.1;
            position: relative;
            display: inline-block;
            padding: 0 25px;
        }
        
        .title-underline {
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background: linear-gradient(to right, transparent, {{ $accentColor }}, transparent);
        }
        
        .certificate-type {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 18px;
            font-weight: 600;
        }
        
        /* Presented section */
        .presented-section {
            text-align: center;
            margin: 40px 0;
        }
        
        .presented-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .student-name-container {
            position: relative;
            display: inline-block;
            margin: 20px 0;
        }
        
        .student-name {
            font-size: 32px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 2px;
            padding: 0 40px 15px;
            border-bottom: 3px solid {{ $accentColor }};
            position: relative;
            min-width: 450px;
            display: inline-block;
        }
        
        .name-flourish {
            position: absolute;
            bottom: -6px;
            width: 20px;
            height: 6px;
            background: {{ $accentColor }};
        }
        
        .flourish-left {
            left: 0;
            border-radius: 3px 0 0 3px;
        }
        
        .flourish-right {
            right: 0;
            border-radius: 0 3px 3px 0;
        }
        
        .student-details {
            font-size: 11px;
            color: #64748b;
            margin-top: 12px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        /* Body text */
        .certificate-body {
            font-size: 14px;
            line-height: 2;
            text-align: center;
            color: #334155;
            margin: 35px 80px;
            font-style: italic;
        }
        
        /* Premium rosette seal */
        .seal-wrapper {
            text-align: center;
            margin: 40px 0;
        }
        
        .rosette-seal {
            display: inline-block;
            position: relative;
            width: 120px;
            height: 120px;
        }
        
        /* Outer petals */
        .rosette-outer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        
        .petal {
            position: absolute;
            width: 40px;
            height: 40px;
            background: {{ $mediumAccent }};
            top: 50%;
            left: 50%;
            margin: -20px 0 0 -20px;
            border-radius: 50% 0;
        }
        
        .petal:nth-child(1) { transform: rotate(0deg) translateX(35px); }
        .petal:nth-child(2) { transform: rotate(45deg) translateX(35px); }
        .petal:nth-child(3) { transform: rotate(90deg) translateX(35px); }
        .petal:nth-child(4) { transform: rotate(135deg) translateX(35px); }
        .petal:nth-child(5) { transform: rotate(180deg) translateX(35px); }
        .petal:nth-child(6) { transform: rotate(225deg) translateX(35px); }
        .petal:nth-child(7) { transform: rotate(270deg) translateX(35px); }
        .petal:nth-child(8) { transform: rotate(315deg) translateX(35px); }
        
        /* Inner circle */
        .rosette-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 70px;
            height: 70px;
            margin: -35px 0 0 -35px;
            border-radius: 50%;
            background: #ffffff;
            border: 4px solid {{ $accentColor }};
            z-index: 2;
        }
        
        .rosette-inner {
            position: absolute;
            top: 6px;
            left: 6px;
            right: 6px;
            bottom: 6px;
            border-radius: 50%;
            border: 2px solid {{ $mediumAccent }};
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: linear-gradient(135deg, #ffffff, {{ $lightAccent }});
        }
        
        .seal-year {
            font-size: 20px;
            font-weight: bold;
            color: {{ $accentColor }};
            line-height: 1;
        }
        
        .seal-text {
            font-size: 7px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 3px;
            font-weight: 600;
        }
        
        /* Signature section */
        .signatures-section {
            margin-top: 50px;
        }
        
        .signatures-container {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .signature-block {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding: 0 30px;
        }
        
        .signature-block.single {
            width: 50%;
            margin: 0 auto;
        }
        
        .signature-image {
            height: 45px;
            max-width: 200px;
            object-fit: contain;
            margin-bottom: 8px;
        }
        
        .signature-line {
            border-top: 2px solid {{ $accentColor }};
            width: 240px;
            margin: 55px auto 0;
            padding-top: 12px;
            position: relative;
        }
        
        .signature-line::before,
        .signature-line::after {
            content: '';
            position: absolute;
            top: -4px;
            width: 6px;
            height: 6px;
            background: {{ $accentColor }};
            border-radius: 50%;
        }
        
        .signature-line::before {
            left: 0;
        }
        
        .signature-line::after {
            right: 0;
        }
        
        .signature-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .signature-name {
            font-size: 13px;
            color: #0f172a;
            font-weight: bold;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }
        
        /* Footer information */
        .certificate-footer {
            margin-top: 45px;
            padding-top: 25px;
            border-top: 2px solid {{ $lightAccent }};
            text-align: center;
            position: relative;
        }
        
        .footer-ornament {
            position: absolute;
            top: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 10px;
            background: #ffffff;
        }
        
        .footer-diamond {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
            width: 10px;
            height: 10px;
            background: {{ $accentColor }};
        }
        
        .certificate-info {
            display: inline-block;
        }
        
        .info-item {
            display: inline-block;
            margin: 0 30px;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
        
        .info-label {
            margin-right: 6px;
        }
        
        .info-value {
            color: #475569;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate-page">
        @if($watermarkPath && file_exists($watermarkPath))
            <img src="{{ $watermarkPath }}" class="watermark" alt="">
        @endif
        
        <!-- Geometric background pattern -->
        <div class="geometric-bg">
            <div class="pattern-circle pattern-tl"></div>
            <div class="pattern-circle pattern-tr"></div>
            <div class="pattern-circle pattern-bl"></div>
            <div class="pattern-circle pattern-br"></div>
            <div class="pattern-line line-1"></div>
            <div class="pattern-line line-2"></div>
        </div>
        
        <!-- Border system -->
        <div class="border-outer">
            <div class="border-middle">
                <div class="border-inner">
                    
                    <!-- Corner ornaments -->
                    <div class="corner-ornament ornament-tl"></div>
                    <div class="corner-ornament ornament-tr"></div>
                    <div class="corner-ornament ornament-bl"></div>
                    <div class="corner-ornament ornament-br"></div>
                    
                    <!-- Corner dots -->
                    <div class="corner-dot dot-tl-1"></div>
                    <div class="corner-dot dot-tl-2"></div>
                    <div class="corner-dot dot-tl-3"></div>
                    <div class="corner-dot dot-tr-1"></div>
                    <div class="corner-dot dot-tr-2"></div>
                    <div class="corner-dot dot-tr-3"></div>
                    <div class="corner-dot dot-bl-1"></div>
                    <div class="corner-dot dot-bl-2"></div>
                    <div class="corner-dot dot-bl-3"></div>
                    <div class="corner-dot dot-br-1"></div>
                    <div class="corner-dot dot-br-2"></div>
                    <div class="corner-dot dot-br-3"></div>
                    
                    <!-- Header -->
                    <div class="certificate-header">
                        @if($showLogo && $logoPath && file_exists($logoPath))
                            <div class="logo-container">
                                <div class="logo-ring"></div>
                                <img src="{{ $logoPath }}" class="logo" alt="Logo">
                            </div>
                        @endif
                        <div class="school-name">{{ $schoolName }}</div>
                    </div>
                    
                    <!-- Ornamental divider -->
                    <div class="ornamental-divider">
                        <div class="divider-line"></div>
                        <div class="divider-center">
                            <div class="divider-diamond"></div>
                        </div>
                    </div>
                    
                    <!-- Title -->
                    <div class="title-section">
                        <h1 class="certificate-title">{{ $certificate->title }}</h1>
                        <div class="title-underline"></div>
                    </div>
                    <div class="certificate-type">{{ $certificate->type }}</div>
                    
                    <!-- Presented to -->
                    <div class="presented-section">
                        <div class="presented-label">This certificate is proudly presented to</div>
                        
                        <div class="student-name-container">
                            <div class="student-name">{{ $student?->full_name }}</div>
                            <div class="name-flourish flourish-left"></div>
                            <div class="name-flourish flourish-right"></div>
                        </div>
                        
                        @if($student?->admission_number || $student?->schoolClass?->name)
                            <div class="student-details">
                                {{ $student?->admission_number }}
                                @if($student?->schoolClass?->name)
                                    • {{ $student?->schoolClass?->name }} {{ $student?->section?->name }}
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Body -->
                    <div class="certificate-body">{{ $certificate->body }}</div>
                    
                    <!-- Rosette Seal -->
                    <div class="seal-wrapper">
                        <div class="rosette-seal">
                            <div class="rosette-outer">
                                <div class="petal"></div>
                                <div class="petal"></div>
                                <div class="petal"></div>
                                <div class="petal"></div>
                                <div class="petal"></div>
                                <div class="petal"></div>
                                <div class="petal"></div>
                                <div class="petal"></div>
                            </div>
                            <div class="rosette-circle">
                                <div class="rosette-inner">
                                    <div class="seal-year">{{ $certificate->issued_on?->format('Y') }}</div>
                                    <div class="seal-text">Official</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Signatures -->
                    <div class="signatures-section">
                        <div class="signatures-container">
                            @if($sig1Label)
                                <div class="signature-block {{ !$sig2Label ? 'single' : '' }}">
                                    @if($sig1ImagePath && file_exists($sig1ImagePath))
                                        <img src="{{ $sig1ImagePath }}" class="signature-image" alt="">
                                    @endif
                                    <div class="signature-line">
                                        <div class="signature-label">{{ $sig1Label }}</div>
                                        @if($sig1Name)
                                            <div class="signature-name">{{ $sig1Name }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            @if($sig2Label)
                                <div class="signature-block">
                                    @if($sig2ImagePath && file_exists($sig2ImagePath))
                                        <img src="{{ $sig2ImagePath }}" class="signature-image" alt="">
                                    @endif
                                    <div class="signature-line">
                                        <div class="signature-label">{{ $sig2Label }}</div>
                                        @if($sig2Name)
                                            <div class="signature-name">{{ $sig2Name }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="certificate-footer">
                        <div class="footer-ornament">
                            <div class="footer-diamond"></div>
                        </div>
                        <div class="certificate-info">
                            <div class="info-item">
                                <span class="info-label">Certificate No:</span>
                                <span class="info-value">{{ $certificate->serial_number }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Issue Date:</span>
                                <span class="info-value">{{ $certificate->issued_on?->format('F j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
