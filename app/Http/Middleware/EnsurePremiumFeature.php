<?php

namespace App\Http\Middleware;

use App\Models\PremiumDevice;
use App\Support\DeviceIdentity;
use App\Support\LicenseManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremiumFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();
        abort_unless($user, 403);

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
        $device = PremiumDevice::query()->where('device_id', $deviceId)->first();
        if (! $device || $device->revoked_at) {
            return $this->deny($request, 'This device is not allowed. Ask an admin to free up a device slot.');
        }

        $limit = $license->deviceLimit();
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

        $user = auth()->user();
        $route = $user?->role === 'admin' ? 'marketplace' : 'more-features';

        return redirect()->route($route)->withErrors(['premium' => $message]);
    }
}
