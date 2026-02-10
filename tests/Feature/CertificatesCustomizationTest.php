<?php

namespace Tests\Feature;

use App\Livewire\Certificates\Index as CertificatesIndex;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CertificatesCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_defaults_can_be_customized_via_config(): void
    {
        $this->seed();

        config([
            'myacademy.certificate_default_type' => 'Character',
            'myacademy.certificate_default_title' => 'Testimonial',
            'myacademy.certificate_default_body' => 'Hello {student_name}',
        ]);

        $teacher = User::query()->where('email', 'teacher@myacademy.local')->firstOrFail();

        Livewire::actingAs($teacher)
            ->test(CertificatesIndex::class)
            ->assertSet('type', 'Character')
            ->assertSet('title', 'Testimonial')
            ->assertSet('body', 'Hello {student_name}');
    }

    public function test_certificate_pdf_can_be_downloaded(): void
    {
        $this->seed();

        config([
            'myacademy.certificate_orientation' => 'portrait',
            'myacademy.certificate_border_color' => '#ff0000',
            'myacademy.certificate_accent_color' => '#00ff00',
            'myacademy.certificate_show_logo' => false,
            'myacademy.certificate_show_watermark' => false,
            'myacademy.certificate_signature_label' => 'Principal',
            'myacademy.certificate_signature_name' => 'Jane Doe',
        ]);

        $teacher = User::query()->where('email', 'teacher@myacademy.local')->firstOrFail();
        $student = Student::query()->firstOrFail();

        $certificate = Certificate::query()->create([
            'student_id' => $student->id,
            'type' => 'General',
            'title' => 'Certificate',
            'body' => 'Test body for {student_name}',
            'issued_on' => '2026-02-07',
            'serial_number' => 'CERT-20260207-TEST01',
            'issued_by' => $teacher->id,
        ]);

        $response = $this->actingAs($teacher)->get(route('certificates.download', $certificate));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
