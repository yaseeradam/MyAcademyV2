<?php

namespace App\Http\Controllers;

use App\Support\LicenseManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Fluent;

class SettingsController extends Controller
{
    private const CERTIFICATE_TEMPLATES = ['modern', 'classic', 'elegant', 'vibrant', 'minimal', 'royal'];
    private const REPORT_CARD_TEMPLATES = ['standard', 'compact', 'elegant', 'modern', 'classic'];

    public function updateSchool(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'school_address' => ['nullable', 'string', 'max:255'],
            'school_phone' => ['nullable', 'string', 'max:50'],
            'school_email' => ['nullable', 'email', 'max:255'],
            'school_logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $settingsPath = storage_path('app/myacademy/settings.json');
        File::ensureDirectoryExists(dirname($settingsPath));

        $settings = [];
        if (File::exists($settingsPath)) {
            $existing = json_decode(File::get($settingsPath), true);
            if (is_array($existing)) {
                $settings = $existing;
            }
        }

        $settings['school_name'] = $data['school_name'];
        $settings['school_address'] = $data['school_address'] ?? null;
        $settings['school_phone'] = $data['school_phone'] ?? null;
        $settings['school_email'] = $data['school_email'] ?? null;

        if ($request->hasFile('school_logo')) {
            $file = $request->file('school_logo');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'school-logo-' . now()->format('YmdHis') . '.' . $ext;
            $path = $file->storeAs('school-assets', $filename, 'uploads');
            $path = str_replace('\\', '/', (string) $path);

            $old = $settings['school_logo'] ?? null;
            if ($old && $old !== $path) {
                Storage::disk('uploads')->delete($old);
            }

            $settings['school_logo'] = $path;
        }

        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', 'School settings saved.');
    }

    public function updateResults(Request $request)
    {
        $data = $request->validate([
            'results_ca1_max' => ['required', 'integer', 'min:0', 'max:200'],
            'results_ca2_max' => ['required', 'integer', 'min:0', 'max:200'],
            'results_exam_max' => ['required', 'integer', 'min:0', 'max:200'],
        ]);

        $total = (int) $data['results_ca1_max'] + (int) $data['results_ca2_max'] + (int) $data['results_exam_max'];
        if ($total <= 0) {
            throw ValidationException::withMessages([
                'results_ca1_max' => 'At least one assessment component must be greater than 0.',
            ]);
        }

        $settingsPath = storage_path('app/myacademy/settings.json');
        File::ensureDirectoryExists(dirname($settingsPath));

        $settings = [];
        if (File::exists($settingsPath)) {
            $existing = json_decode(File::get($settingsPath), true);
            if (is_array($existing)) {
                $settings = $existing;
            }
        }

        $settings['results_ca1_max'] = (int) $data['results_ca1_max'];
        $settings['results_ca2_max'] = (int) $data['results_ca2_max'];
        $settings['results_exam_max'] = (int) $data['results_exam_max'];

        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', 'Result scoring marks saved.');
    }

    public function updateCertificates(Request $request)
    {
        $data = $request->validate([
            'certificate_orientation' => ['required', 'string', 'in:landscape,portrait'],
            'certificate_border_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'certificate_accent_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'certificate_show_logo' => ['required', 'boolean'],
            'certificate_show_watermark' => ['required', 'boolean'],
            'certificate_watermark_image' => ['nullable', 'image', 'max:4096'],
            'certificate_watermark_remove' => ['nullable', 'boolean'],

            'certificate_signature_label' => ['nullable', 'string', 'max:50'],
            'certificate_signature_name' => ['nullable', 'string', 'max:100'],
            'certificate_signature_image' => ['nullable', 'image', 'max:4096'],
            'certificate_signature_remove' => ['nullable', 'boolean'],

            'certificate_signature2_label' => ['nullable', 'string', 'max:50'],
            'certificate_signature2_name' => ['nullable', 'string', 'max:100'],
            'certificate_signature2_image' => ['nullable', 'image', 'max:4096'],
            'certificate_signature2_remove' => ['nullable', 'boolean'],

            'certificate_default_type' => ['nullable', 'string', 'max:50'],
            'certificate_default_title' => ['nullable', 'string', 'max:255'],
            'certificate_default_body' => ['nullable', 'string', 'max:8000'],
        ]);

        $settingsPath = storage_path('app/myacademy/settings.json');
        File::ensureDirectoryExists(dirname($settingsPath));

        $settings = [];
        if (File::exists($settingsPath)) {
            $existing = json_decode(File::get($settingsPath), true);
            if (is_array($existing)) {
                $settings = $existing;
            }
        }

        $settings['certificate_orientation'] = $data['certificate_orientation'];
        $settings['certificate_border_color'] = $data['certificate_border_color'];
        $settings['certificate_accent_color'] = $data['certificate_accent_color'];
        $settings['certificate_show_logo'] = (bool) $data['certificate_show_logo'];
        $settings['certificate_show_watermark'] = (bool) $data['certificate_show_watermark'];

        if ($request->boolean('certificate_watermark_remove')) {
            $old = $settings['certificate_watermark_image'] ?? null;
            if ($old) {
                Storage::disk('uploads')->delete((string) $old);
            }
            $settings['certificate_watermark_image'] = null;
        }

        if ($request->hasFile('certificate_watermark_image')) {
            $file = $request->file('certificate_watermark_image');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'certificate-watermark-' . now()->format('YmdHis') . '.' . $ext;
            $path = $file->storeAs('school-assets', $filename, 'uploads');
            $path = str_replace('\\', '/', (string) $path);

            $old = $settings['certificate_watermark_image'] ?? null;
            if ($old && $old !== $path) {
                Storage::disk('uploads')->delete((string) $old);
            }

            $settings['certificate_watermark_image'] = $path;
        }

        $settings['certificate_signature_label'] = $data['certificate_signature_label'] ?: null;
        $settings['certificate_signature_name'] = $data['certificate_signature_name'] ?: null;

        if ($request->boolean('certificate_signature_remove')) {
            $old = $settings['certificate_signature_image'] ?? null;
            if ($old) {
                Storage::disk('uploads')->delete((string) $old);
            }
            $settings['certificate_signature_image'] = null;
        }

        if ($request->hasFile('certificate_signature_image')) {
            $file = $request->file('certificate_signature_image');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'certificate-signature-' . now()->format('YmdHis') . '.' . $ext;
            $path = $file->storeAs('school-assets', $filename, 'uploads');
            $path = str_replace('\\', '/', (string) $path);

            $old = $settings['certificate_signature_image'] ?? null;
            if ($old && $old !== $path) {
                Storage::disk('uploads')->delete((string) $old);
            }

            $settings['certificate_signature_image'] = $path;
        }

        $settings['certificate_signature2_label'] = $data['certificate_signature2_label'] ?: null;
        $settings['certificate_signature2_name'] = $data['certificate_signature2_name'] ?: null;

        if ($request->boolean('certificate_signature2_remove')) {
            $old = $settings['certificate_signature2_image'] ?? null;
            if ($old) {
                Storage::disk('uploads')->delete((string) $old);
            }
            $settings['certificate_signature2_image'] = null;
        }

        if ($request->hasFile('certificate_signature2_image')) {
            $file = $request->file('certificate_signature2_image');
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $filename = 'certificate-signature2-' . now()->format('YmdHis') . '.' . $ext;
            $path = $file->storeAs('school-assets', $filename, 'uploads');
            $path = str_replace('\\', '/', (string) $path);

            $old = $settings['certificate_signature2_image'] ?? null;
            if ($old && $old !== $path) {
                Storage::disk('uploads')->delete((string) $old);
            }

            $settings['certificate_signature2_image'] = $path;
        }

        $settings['certificate_default_type'] = $data['certificate_default_type'] ?: null;
        $settings['certificate_default_title'] = $data['certificate_default_title'] ?: null;
        $settings['certificate_default_body'] = $data['certificate_default_body'] ?: null;

        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', 'Certificate settings saved.');
    }

    public function updateTemplates(Request $request)
    {
        $data = $request->validate([
            'certificate_template' => ['required', 'string', 'in:' . implode(',', self::CERTIFICATE_TEMPLATES)],
            'report_card_template' => ['required', 'string', 'in:' . implode(',', self::REPORT_CARD_TEMPLATES)],
        ]);

        $settingsPath = storage_path('app/myacademy/settings.json');
        File::ensureDirectoryExists(dirname($settingsPath));

        $settings = [];
        if (File::exists($settingsPath)) {
            $existing = json_decode(File::get($settingsPath), true);
            if (is_array($existing)) {
                $settings = $existing;
            }
        }

        $settings['certificate_template'] = (string) $data['certificate_template'];
        $settings['report_card_template'] = (string) $data['report_card_template'];

        File::put($settingsPath, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return back()->with('status', 'Template selection saved.');
    }

    public function previewTemplate(string $type, string $template): \Illuminate\Http\Response
    {
        $type = strtolower(trim($type));
        $template = strtolower(trim($template));

        if ($type === 'certificate') {
            abort_unless(in_array($template, self::CERTIFICATE_TEMPLATES, true), 404);

            $view = match ($template) {
                'classic' => 'pdf.certificate-classic',
                'elegant' => 'pdf.certificate-elegant',
                'vibrant' => 'pdf.certificate-vibrant',
                'minimal' => 'pdf.certificate-minimal',
                'royal' => 'pdf.certificate-royal',
                default => 'pdf.certificate',
            };

            $student = new Fluent([
                'full_name' => 'Jane Doe',
                'admission_number' => 'ADM/001',
                'schoolClass' => new Fluent(['name' => 'JSS 1']),
                'section' => new Fluent(['name' => 'A']),
            ]);

            $certificate = new Fluent([
                'title' => 'Certificate of Achievement',
                'type' => 'General',
                'body' => 'This certificate is proudly presented to {student_name} for outstanding performance and dedication.',
                'serial_number' => 'CERT-0001',
                'issued_on' => Carbon::now(),
            ]);

            $orientation = (string) config('myacademy.certificate_orientation', 'landscape');
            $orientation = in_array($orientation, ['landscape', 'portrait'], true) ? $orientation : 'landscape';

            $pdf = Pdf::loadView($view, [
                'certificate' => $certificate,
                'student' => $student,
            ])->setPaper('a4', $orientation);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certificate-template-preview.pdf"',
            ]);
        }

        if ($type === 'report-card') {
            abort_unless(in_array($template, self::REPORT_CARD_TEMPLATES, true), 404);

            $view = match ($template) {
                'compact' => 'pdf.report-card-compact',
                'elegant' => 'pdf.report-card-elegant',
                'modern' => 'pdf.report-card-modern',
                'classic' => 'pdf.report-card-classic',
                default => 'pdf.report-card',
            };

            $student = new Fluent([
                'full_name' => 'Jane Doe',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'admission_number' => 'ADM/001',
                'schoolClass' => new Fluent(['name' => 'JSS 1']),
                'section' => new Fluent(['name' => 'A']),
            ]);

            $rows = collect([
                ['subject' => new Fluent(['name' => 'Mathematics']), 'ca1' => 18, 'ca2' => 19, 'exam' => 55, 'total' => 92, 'grade' => 'A'],
                ['subject' => new Fluent(['name' => 'English']), 'ca1' => 16, 'ca2' => 18, 'exam' => 50, 'total' => 84, 'grade' => 'A'],
                ['subject' => new Fluent(['name' => 'Basic Science']), 'ca1' => 15, 'ca2' => 14, 'exam' => 49, 'total' => 78, 'grade' => 'B'],
                ['subject' => new Fluent(['name' => 'Social Studies']), 'ca1' => 17, 'ca2' => 15, 'exam' => 45, 'total' => 77, 'grade' => 'B'],
                ['subject' => new Fluent(['name' => 'Computer Studies']), 'ca1' => 19, 'ca2' => 18, 'exam' => 52, 'total' => 89, 'grade' => 'A'],
            ]);

            $grandTotal = (int) $rows->sum(fn($r) => (int) ($r['total'] ?? 0));
            $subjectCount = max(1, (int) $rows->count());
            $average = round($grandTotal / $subjectCount, 2);

            $pdf = Pdf::loadView($view, [
                'student' => $student,
                'term' => 1,
                'session' => '2025/2026',
                'rows' => $rows,
                'grandTotal' => $grandTotal,
                'average' => $average,
                'position' => 1,
                'classAverage' => $average,
                'totalStudents' => 35,
                'timesOpened' => 65,
                'timesPresent' => 60,
                'timesAbsent' => 5,
                'teacherRemarks' => 'An excellent student with outstanding academic performance. Keep it up!',
                'principalRemarks' => 'A commendable result. Continue to strive for excellence.',
                'nextTermDate' => 'September 8, 2025',
            ])->setPaper('a4');

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="report-card-template-preview.pdf"',
            ]);
        }

        abort(404);
    }

    public function updateLicense(Request $request, LicenseManager $licenses)
    {
        $request->validate([
            'license' => ['required', 'file', 'max:64'],
        ]);

        $realPath = $request->file('license')->getRealPath();
        $raw = $realPath ? (string) File::get($realPath) : '';

        $state = $licenses->installRaw($raw);

        if (!($state['ok'] ?? false)) {
            return back()->withErrors(['license' => (string) ($state['reason'] ?? 'Invalid license.')]);
        }

        return back()->with('status', 'License installed. Premium features updated.');
    }
}
