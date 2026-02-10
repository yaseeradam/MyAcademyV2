<?php

namespace Tests\Feature;

use App\Models\PremiumDevice;
use App\Models\User;
use App\Support\DeviceIdentity;
use App\Support\LicenseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PremiumLicenseMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_middleware_allows_when_licensed_and_within_device_limit(): void
    {
        if (! function_exists('sodium_crypto_sign_keypair')) {
            $this->markTestSkipped('Sodium is not available.');
        }

        Route::middleware(['web', 'auth', 'premium:cbt'])->get('/__premium_test', fn () => 'ok');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);
        config(['myacademy.license_public_key' => base64_encode($publicKey)]);

        $payload = [
            'school_id' => 'school-1',
            'features' => ['cbt'],
            'issued_at' => Carbon::now()->toISOString(),
            'expires_at' => Carbon::now()->addDays(10)->toISOString(),
            'device_limit' => 1,
        ];
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sig = sodium_crypto_sign_detached($payloadJson, $secretKey);
        $raw = json_encode(['v' => 1, 'payload' => base64_encode($payloadJson), 'sig' => base64_encode($sig)], JSON_UNESCAPED_SLASHES);

        app(LicenseManager::class)->installRaw($raw);

        PremiumDevice::query()->create([
            'device_id' => DeviceIdentity::id(),
            'label' => 'Test Device',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        $this->actingAs($admin)->get('/__premium_test')->assertOk();
    }

    public function test_premium_middleware_denies_when_device_limit_exceeded(): void
    {
        if (! function_exists('sodium_crypto_sign_keypair')) {
            $this->markTestSkipped('Sodium is not available.');
        }

        Route::middleware(['web', 'auth', 'premium:cbt'])->get('/__premium_test2', fn () => 'ok');

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $keypair = sodium_crypto_sign_keypair();
        $publicKey = sodium_crypto_sign_publickey($keypair);
        $secretKey = sodium_crypto_sign_secretkey($keypair);
        config(['myacademy.license_public_key' => base64_encode($publicKey)]);

        $payload = [
            'school_id' => 'school-1',
            'features' => ['cbt'],
            'issued_at' => Carbon::now()->toISOString(),
            'expires_at' => Carbon::now()->addDays(10)->toISOString(),
            'device_limit' => 1,
        ];
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $sig = sodium_crypto_sign_detached($payloadJson, $secretKey);
        $raw = json_encode(['v' => 1, 'payload' => base64_encode($payloadJson), 'sig' => base64_encode($sig)], JSON_UNESCAPED_SLASHES);

        app(LicenseManager::class)->installRaw($raw);

        PremiumDevice::query()->create([
            'device_id' => DeviceIdentity::id(),
            'label' => 'Current Device',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        PremiumDevice::query()->create([
            'device_id' => 'another-device',
            'label' => 'Another Device',
            'first_seen_at' => now(),
            'last_seen_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/__premium_test2')
            ->assertRedirect(route('settings'));
    }
}

