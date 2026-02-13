<?php

return [
    'mode' => env('MYACADEMY_MODE', 'full'), // full|cbt
    'premium_enforce' => (bool) env('MYACADEMY_PREMIUM_ENFORCE', true),

    // Used to bind a license to a specific school installation/customer.
    // Recommended: set a unique value (UUID) per client deployment and issue licenses for that school_id.
    'school_id' => env('MYACADEMY_SCHOOL_ID', ''),

    'school_name' => env('MYACADEMY_SCHOOL_NAME', env('APP_NAME', 'MyAcademy')),
    'tagline' => env('MYACADEMY_SCHOOL_TAGLINE', "Here's what's happening in your school today."),
    'current_term' => env('MYACADEMY_CURRENT_TERM', 'Term 1'),
    'current_week' => env('MYACADEMY_CURRENT_WEEK', 'Week 1'),
    'currency_symbol' => env('MYACADEMY_CURRENCY_SYMBOL', '₦'),

    'school_address' => env('MYACADEMY_SCHOOL_ADDRESS'),
    'school_phone' => env('MYACADEMY_SCHOOL_PHONE'),
    'school_email' => env('MYACADEMY_SCHOOL_EMAIL'),
    'school_logo' => env('MYACADEMY_SCHOOL_LOGO'),

    'results_ca1_max' => (int) env('MYACADEMY_RESULTS_CA1_MAX', 20),
    'results_ca2_max' => (int) env('MYACADEMY_RESULTS_CA2_MAX', 20),
    'results_exam_max' => (int) env('MYACADEMY_RESULTS_EXAM_MAX', 60),

    'certificate_orientation' => env('MYACADEMY_CERTIFICATE_ORIENTATION', 'landscape'),
    'certificate_border_color' => env('MYACADEMY_CERTIFICATE_BORDER_COLOR', '#0ea5e9'),
    'certificate_accent_color' => env('MYACADEMY_CERTIFICATE_ACCENT_COLOR', '#0ea5e9'),
    'certificate_show_logo' => (bool) env('MYACADEMY_CERTIFICATE_SHOW_LOGO', true),
    'certificate_show_watermark' => (bool) env('MYACADEMY_CERTIFICATE_SHOW_WATERMARK', false),
    'certificate_watermark_image' => env('MYACADEMY_CERTIFICATE_WATERMARK_IMAGE'),

    'certificate_signature_label' => env('MYACADEMY_CERTIFICATE_SIGNATURE_LABEL', 'Authorized Signature'),
    'certificate_signature_name' => env('MYACADEMY_CERTIFICATE_SIGNATURE_NAME'),
    'certificate_signature_image' => env('MYACADEMY_CERTIFICATE_SIGNATURE_IMAGE'),

    'certificate_signature2_label' => env('MYACADEMY_CERTIFICATE_SIGNATURE2_LABEL'),
    'certificate_signature2_name' => env('MYACADEMY_CERTIFICATE_SIGNATURE2_NAME'),
    'certificate_signature2_image' => env('MYACADEMY_CERTIFICATE_SIGNATURE2_IMAGE'),

    'certificate_default_type' => env('MYACADEMY_CERTIFICATE_DEFAULT_TYPE', 'General'),
    'certificate_default_title' => env('MYACADEMY_CERTIFICATE_DEFAULT_TITLE', 'Certificate'),
    'certificate_default_body' => env('MYACADEMY_CERTIFICATE_DEFAULT_BODY'),

    'premium_device_removal_limit' => (int) env('MYACADEMY_PREMIUM_DEVICE_REMOVAL_LIMIT', 2),
    'premium_device_removal_window_days' => (int) env('MYACADEMY_PREMIUM_DEVICE_REMOVAL_WINDOW_DAYS', 30),

    // Premium licensing (Ed25519)
    // Set this to the base64 encoded Ed25519 public key used to verify licenses.
    'license_public_key' => env('MYACADEMY_LICENSE_PUBLIC_KEY', ''),
];
