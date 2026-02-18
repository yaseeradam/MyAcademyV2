<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class CertificateController extends Controller
{
    public function download(Certificate $certificate): Response
    {
        $user = auth()->user();
        abort_unless($user, 403);

        abort_unless(in_array($user->role, ['admin', 'teacher'], true), 403);

        $certificate->load(['student.schoolClass', 'student.section', 'issuer']);

        $student = $certificate->student;

        $template = (string) config('myacademy.certificate_template', 'modern');
        $view = $template === 'classic' ? 'pdf.certificate-classic' : 'pdf.certificate';

        $orientation = (string) config('myacademy.certificate_orientation', 'landscape');
        $orientation = in_array($orientation, ['landscape', 'portrait'], true) ? $orientation : 'landscape';

        $pdf = Pdf::loadView($view, [
            'certificate' => $certificate,
            'student' => $student,
        ])->setPaper('a4', $orientation);

        $filename = 'certificate-'.$certificate->serial_number.'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
