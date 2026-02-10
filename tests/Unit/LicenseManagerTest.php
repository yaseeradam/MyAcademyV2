<?php

namespace Tests\Unit;

use App\Support\LicenseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LicenseManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_license_is_verified(): void
    {
        if (! function_exists('sodium_crypto_sign_keypair')) {
            $this->markTestSkipped('Sodium is not available.');
        }

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);

        config(['myacademy.license_public_key' => base64_encode($publicKey)]);

        $payload = [
            'school_id' => 'school-1',
            'features' => ['cbt', 'savings_loan'],
            'issued_at' => Carbon::parse('2026-02-10 10:00:00')->toISOString(),
            'expires_at' => Carbon::now()->addDays(10)->toISOString(),
            'device_limit' => 50,
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sig = sodium_crypto_sign_detached($payloadJson, $secretKey);

        $raw = json_encode([
            'v' => 1,
            'payload' => base64_encode($payloadJson),
            'sig' => base64_encode($sig),
        ], JSON_UNESCAPED_SLASHES);

        $lm = app(LicenseManager::class);
        $state = $lm->verifyRaw($raw);

        $this->assertTrue($state['ok']);
        $this->assertSame(50, $state['data']['device_limit']);
        $this->assertContains('cbt', $state['data']['features']);
    }

    public function test_expired_license_is_rejected(): void
    {
        if (! function_exists('sodium_crypto_sign_keypair')) {
            $this->markTestSkipped('Sodium is not available.');
        }

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);

        config(['myacademy.license_public_key' => base64_encode($publicKey)]);

        $payload = [
            'school_id' => 'school-1',
            'features' => ['cbt'],
            'issued_at' => Carbon::now()->subDays(10)->toISOString(),
            'expires_at' => Carbon::now()->subDay()->toISOString(),
            'device_limit' => 50,
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sig = sodium_crypto_sign_detached($payloadJson, $secretKey);

        $raw = json_encode([
            'v' => 1,
            'payload' => base64_encode($payloadJson),
            'sig' => base64_encode($sig),
        ], JSON_UNESCAPED_SLASHES);

        $lm = app(LicenseManager::class);
        $state = $lm->verifyRaw($raw);

        $this->assertFalse($state['ok']);
        $this->assertSame('License expired.', $state['reason']);
    }

    public function test_device_limit_over_1000_is_rejected(): void
    {
        if (! function_exists('sodium_crypto_sign_keypair')) {
            $this->markTestSkipped('Sodium is not available.');
        }

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);

        config(['myacademy.license_public_key' => base64_encode($publicKey)]);

        $payload = [
            'school_id' => 'school-1',
            'features' => ['cbt'],
            'issued_at' => Carbon::now()->toISOString(),
            'expires_at' => Carbon::now()->addDays(10)->toISOString(),
            'device_limit' => 1001,
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sig = sodium_crypto_sign_detached($payloadJson, $secretKey);

        $raw = json_encode([
            'v' => 1,
            'payload' => base64_encode($payloadJson),
            'sig' => base64_encode($sig),
        ], JSON_UNESCAPED_SLASHES);

        $lm = app(LicenseManager::class);
        $state = $lm->verifyRaw($raw);

        $this->assertFalse($state['ok']);
        $this->assertSame('Device limit exceeds maximum (1000).', $state['reason']);
    }
}

