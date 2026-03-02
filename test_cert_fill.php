<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Certificate;
use App\Models\Student;
use App\Support\CertificatePdf;

try {
    $c = Certificate::first() ?: new Certificate(['title' => 'Test']);
    $s = Student::first() ?: new Student(['first_name' => 'John']);
    $pdf = CertificatePdf::fromView('pdf.certificate', ['certificate' => $c, 'student' => $s]);
    file_put_contents('storage/app/test.pdf', $pdf);
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
