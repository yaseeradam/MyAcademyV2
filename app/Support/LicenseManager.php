<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class LicenseManager
{
    private const LICENSE_PATH = 'app/myacademy/license.json';
    private const LAST_SEEN_PATH = 'app/myacademy/license-last-seen.txt';

    public function load(): array
    {
        $path = storage_path(self::LICENSE_PATH);
        if (! File::exists($path)) {
            return ['ok' => false, 'reason' => 'No license installed.'];
        }

        $raw = (string) File::get($path);
        return $this->verifyRaw($raw);
    }

    public function verifyRaw(string $raw): array
    {
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return ['ok' => false, 'reason' => 'Invalid license file.'];
        }

        if (($decoded['v'] ?? null) !== 1) {
            return ['ok' => false, 'reason' => 'Unsupported license version.'];
        }

        $payloadB64 = (string) ($decoded['payload'] ?? '');
        $sigB64 = (string) ($decoded['sig'] ?? '');

        $payload = base64_decode($payloadB64, true);
        $sig = base64_decode($sigB64, true);

        if ($payload === false || $sig === false) {
            return ['ok' => false, 'reason' => 'Invalid license encoding.'];
        }

        $pubB64 = (string) config('myacademy.license_public_key');
        $pub = base64_decode($pubB64, true);
        if ($pub === false || $pubB64 === '') {
            return ['ok' => false, 'reason' => 'License public key not configured.'];
        }

        if (! function_exists('sodium_crypto_sign_verify_detached')) {
            return ['ok' => false, 'reason' => 'Sodium extension is not available.'];
        }

        try {
            $valid = sodium_crypto_sign_verify_detached($sig, $payload, $pub);
        } catch (\Throwable) {
            $valid = false;
        }

        if (! $valid) {
            return ['ok' => false, 'reason' => 'License signature is invalid.'];
        }

        $data = json_decode($payload, true);
        if (! is_array($data)) {
            return ['ok' => false, 'reason' => 'Invalid license payload.'];
        }

        $expiresAt = $this->parseDate($data['expires_at'] ?? null);
        if (! $expiresAt) {
            return ['ok' => false, 'reason' => 'License expiry is missing.'];
        }

        $deviceLimit = (int) ($data['device_limit'] ?? 0);
        if ($deviceLimit <= 0) {
            return ['ok' => false, 'reason' => 'Device limit is missing.'];
        }
        if ($deviceLimit > 1000) {
            return ['ok' => false, 'reason' => 'Device limit exceeds maximum (1000).'];
        }

        $features = $data['features'] ?? [];
        if (! is_array($features)) {
            $features = [];
        }
        $features = array_values(array_unique(array_filter(array_map('strval', $features))));

        $now = now();
        if ($expiresAt->lt($now)) {
            return ['ok' => false, 'reason' => 'License expired.', 'data' => $data];
        }

        $timeGuardOk = $this->timeGuardPasses($now);
        if (! $timeGuardOk) {
            return ['ok' => false, 'reason' => 'System time appears to be incorrect.', 'data' => $data];
        }

        $this->updateLastSeen($now);

        $data['features'] = $features;
        $data['device_limit'] = $deviceLimit;
        $data['expires_at'] = $expiresAt->toISOString();

        return ['ok' => true, 'data' => $data];
    }

    public function can(string $feature): bool
    {
        $state = $this->load();
        if (! ($state['ok'] ?? false)) {
            return false;
        }

        $features = $state['data']['features'] ?? [];
        if (! is_array($features)) {
            return false;
        }

        return in_array($feature, $features, true);
    }

    public function deviceLimit(): int
    {
        $state = $this->load();
        if (! ($state['ok'] ?? false)) {
            return 0;
        }

        return (int) ($state['data']['device_limit'] ?? 0);
    }

    public function expiresAt(): ?Carbon
    {
        $state = $this->load();
        if (! ($state['ok'] ?? false)) {
            return null;
        }

        return $this->parseDate($state['data']['expires_at'] ?? null);
    }

    public function reason(): ?string
    {
        $state = $this->load();
        if (($state['ok'] ?? false) === true) {
            return null;
        }

        return (string) ($state['reason'] ?? 'Premium is locked.');
    }

    public function installRaw(string $raw): array
    {
        $state = $this->verifyRaw($raw);
        if (! ($state['ok'] ?? false)) {
            return $state;
        }

        $path = storage_path(self::LICENSE_PATH);
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $raw);

        return $state;
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function timeGuardPasses(Carbon $now): bool
    {
        $path = storage_path(self::LAST_SEEN_PATH);
        if (! File::exists($path)) {
            return true;
        }

        $raw = trim((string) File::get($path));
        if ($raw === '' || ! ctype_digit($raw)) {
            return true;
        }

        $lastSeen = (int) $raw;
        $backwardsSeconds = $lastSeen - $now->getTimestamp();

        // Allow small clock corrections; block only if time goes back significantly.
        return $backwardsSeconds <= 172800; // 2 days
    }

    private function updateLastSeen(Carbon $now): void
    {
        try {
            $path = storage_path(self::LAST_SEEN_PATH);
            File::ensureDirectoryExists(dirname($path));
            File::put($path, (string) $now->getTimestamp());
        } catch (\Throwable) {
            // ignore
        }
    }
}

