<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $path = storage_path('app/myacademy/settings.json');

        if (! File::exists($path)) {
            return;
        }

        $raw = File::get($path);
        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return;
        }

        $allowed = [
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_logo',
            'currency_symbol',
            'current_term',
            'current_week',
            'tagline',
            'results_ca1_max',
            'results_ca2_max',
            'results_exam_max',
            'certificate_orientation',
            'certificate_border_color',
            'certificate_accent_color',
            'certificate_show_logo',
            'certificate_show_watermark',
            'certificate_watermark_image',
            'certificate_signature_label',
            'certificate_signature_name',
            'certificate_signature_image',
            'certificate_signature2_label',
            'certificate_signature2_name',
            'certificate_signature2_image',
            'certificate_default_type',
            'certificate_default_title',
            'certificate_default_body',
        ];

        $settings = Arr::only($data, $allowed);
        foreach ($settings as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            config(["myacademy.{$key}" => $value]);
        }
    }
}
