@php
    $schoolName = config('myacademy.school_name', config('app.name', 'MyAcademy'));
    $logo = config('myacademy.school_logo');
    $logoPath = $logo ? public_path('uploads/'.str_replace('\\', '/', $logo)) : null;

    $borderColor = config('myacademy.certificate_border_color', '#0ea5e9');
    $accentColor = config('myacademy.certificate_accent_color', '#0ea5e9');
    $showLogo = (bool) config('myacademy.certificate_show_logo', true);
    $showWatermark = (bool) config('myacademy.certificate_show_watermark', false);
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

    $issuedOn = $certificate?->issued_on ?? $certificate?->issue_date ?? now();

    $key = $certificate?->template ? (string) $certificate->template : '';
    $key = trim($key);
    if (str_ends_with(strtolower($key), '.png')) {
        $key = substr($key, 0, -4);
    }
    $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key) ?: '';
    $bgTemplate = $key !== '' ? public_path('certificates/templates/'.$key.'.png') : null;
    $bgTemplate = $bgTemplate && file_exists($bgTemplate) ? $bgTemplate : null;
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ $certificate?->title ?? 'Certificate' }}</title>
        <style>
            @page { margin: 18px; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: DejaVu Sans, Arial, sans-serif; color: #0f172a; }

            .page {
                position: relative;
                width: 100%;
                height: 100%;
                border: 4px solid {{ $borderColor }};
                padding: 22px;
                overflow: hidden;
            }
            .inner {
                position: relative;
                border: 2px solid {{ $accentColor }};
                padding: 18px;
                height: 100%;
                background: #ffffff;
            }
            .bg {
                position: absolute;
                inset: 0;
                z-index: 0;
            }
            .bg img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0.18;
            }
            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.06;
                width: 520px;
                height: 520px;
                object-fit: contain;
                z-index: 0;
            }
            .content { position: relative; z-index: 1; text-align: center; }

            .header {
                display: table;
                width: 100%;
                margin-bottom: 8px;
            }
            .header-cell { display: table-cell; vertical-align: middle; }
            .header-left { width: 110px; }
            .logo {
                width: 88px;
                height: 88px;
                object-fit: contain;
                border: 3px solid {{ $accentColor }};
                border-radius: 10px;
                padding: 6px;
                background: #fff;
            }
            .school-name {
                font-size: 20px;
                font-weight: 800;
                letter-spacing: 1px;
                text-transform: uppercase;
            }
            .divider {
                margin: 12px auto 16px;
                width: 72%;
                border-top: 2px solid {{ $accentColor }};
            }

            .title {
                font-size: 34px;
                font-weight: 900;
                margin-top: 4px;
            }
            .type {
                margin-top: 6px;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 2px;
                text-transform: uppercase;
                color: #334155;
            }

            .presented {
                margin-top: 18px;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 1px;
                text-transform: uppercase;
                color: #475569;
            }
            .student {
                margin-top: 10px;
                font-size: 28px;
                font-weight: 900;
                color: {{ $accentColor }};
            }
            .student-meta {
                margin-top: 6px;
                font-size: 10px;
                color: #475569;
                font-weight: 700;
            }

            .body {
                margin: 18px auto 0;
                width: 84%;
                font-size: 13px;
                line-height: 1.6;
                color: #0f172a;
            }

            .meta-row {
                margin-top: 14px;
                font-size: 10px;
                color: #475569;
                font-weight: 700;
                letter-spacing: 0.3px;
            }

            .footer {
                position: absolute;
                left: 40px;
                right: 40px;
                bottom: 28px;
                display: table;
                width: calc(100% - 80px);
            }
            .sig {
                display: table-cell;
                width: 50%;
                text-align: center;
                vertical-align: bottom;
            }
            .sig-line {
                margin: 8px auto 0;
                width: 200px;
                border-top: 1px solid #0f172a;
            }
            .sig-label {
                margin-top: 6px;
                font-size: 10px;
                font-weight: 800;
                color: #475569;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .sig-name { margin-top: 2px; font-size: 10px; color: #334155; font-weight: 700; }
            .sig-img { height: 54px; object-fit: contain; display: block; margin: 0 auto; }
        </style>
    </head>
    <body>
        @if (isset($imagePath) && is_string($imagePath) && $imagePath !== '')
            <style>
                body { margin: 0; padding: 0; }
                img { width: 100%; height: auto; display: block; }
            </style>
            <img src="{{ $imagePath }}" alt="Certificate">
        @else
            <div class="page">
                <div class="inner">
                    @if($bgTemplate)
                        <div class="bg">
                            <img src="{{ $bgTemplate }}" alt="Template background">
                        </div>
                    @endif

                    @if($watermarkPath && file_exists($watermarkPath))
                        <img class="watermark" src="{{ $watermarkPath }}" alt="Watermark">
                    @endif

                    <div class="content">
                        <div class="header">
                            <div class="header-cell header-left">
                                @if($showLogo && $logoPath && file_exists($logoPath))
                                    <img class="logo" src="{{ $logoPath }}" alt="Logo">
                                @endif
                            </div>
                            <div class="header-cell">
                                <div class="school-name">{{ $schoolName }}</div>
                            </div>
                            <div class="header-cell header-left"></div>
                        </div>

                        <div class="divider"></div>

                        <div class="title">{{ $certificate?->title ?? 'Certificate' }}</div>
                        <div class="type">{{ $certificate?->type ?? 'General' }}</div>

                        <div class="presented">Presented to</div>
                        <div class="student">{{ $student?->full_name }}</div>
                        @if($student?->admission_number || $student?->schoolClass?->name)
                            <div class="student-meta">
                                {{ $student?->admission_number }}
                                @if($student?->schoolClass?->name)
                                    • {{ $student?->schoolClass?->name }} {{ $student?->section?->name }}
                                @endif
                            </div>
                        @endif

                        <div class="body">{{ $certificate?->body }}</div>

                        <div class="meta-row">
                            <span>Serial: {{ $certificate?->serial_number }}</span>
                            <span style="margin-left: 16px;">Date: {{ $issuedOn?->format('F j, Y') }}</span>
                        </div>
                    </div>

                    <div class="footer">
                        @if($sig1Label)
                            <div class="sig">
                                @if($sig1ImagePath && file_exists($sig1ImagePath))
                                    <img class="sig-img" src="{{ $sig1ImagePath }}" alt="">
                                @endif
                                <div class="sig-line"></div>
                                <div class="sig-label">{{ $sig1Label }}</div>
                                @if($sig1Name)
                                    <div class="sig-name">{{ $sig1Name }}</div>
                                @endif
                            </div>
                        @endif

                        @if($sig2Label)
                            <div class="sig">
                                @if($sig2ImagePath && file_exists($sig2ImagePath))
                                    <img class="sig-img" src="{{ $sig2ImagePath }}" alt="">
                                @endif
                                <div class="sig-line"></div>
                                <div class="sig-label">{{ $sig2Label }}</div>
                                @if($sig2Name)
                                    <div class="sig-name">{{ $sig2Name }}</div>
                                @endif
                            </div>
                        @else
                            <div class="sig"></div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>
