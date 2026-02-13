<?php

namespace App\Http\Middleware;

use App\Models\PremiumDevice;
use App\Support\DeviceIdentity;
use App\Support\LicenseManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremiumPublicFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $license = app(LicenseManager::class);
        $state = $license->load();

        if (! ($state['ok'] ?? false)) {
            return $this->deny($request, (string) ($state['reason'] ?? 'Premium is locked.'));
        }

        if (! $license->can($feature)) {
            return $this->deny($request, 'This premium feature is not enabled for your license.');
        }

        if (! Schema::hasTable('premium_devices')) {
            return $this->deny($request, 'Premium device tracking is not available.');
        }

        $deviceId = DeviceIdentity::id();
        $label = DeviceIdentity::label();

        $limit = $license->deviceLimit();

        $device = PremiumDevice::query()->where('device_id', $deviceId)->first();
        if ($device && $device->revoked_at) {
            return $this->deny($request, 'This device is not allowed. Ask an admin to free up a device slot.');
        }

        if (! $device) {
            $activeCount = (int) PremiumDevice::query()->whereNull('revoked_at')->count();
            if ($limit > 0 && $activeCount >= $limit) {
                return $this->deny($request, 'Device limit reached. Remove an old device or upgrade your license.');
            }

            $device = PremiumDevice::query()->create([
                'device_id' => $deviceId,
                'label' => $label,
                'first_seen_at' => now(),
                'last_seen_at' => now(),
            ]);
        } else {
            $device->forceFill([
                'label' => $label,
                'last_seen_at' => now(),
            ])->save();
        }

        $activeCount = (int) PremiumDevice::query()->whereNull('revoked_at')->count();
        if ($limit > 0 && $activeCount > $limit) {
            return $this->deny($request, 'Device limit reached. Remove an old device or upgrade your license.');
        }

        return $next($request);
    }

    private function deny(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            abort(403, $message);
        }

        abort(403, $message);
    }
}

