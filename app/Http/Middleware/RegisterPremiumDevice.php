<?php

namespace App\Http\Middleware;

use App\Models\PremiumDevice;
use App\Support\DeviceIdentity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RegisterPremiumDevice
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningInConsole() || app()->runningUnitTests()) {
            return $next($request);
        }

        if (! auth()->check()) {
            return $next($request);
        }

        try {
            if (! Schema::hasTable('premium_devices')) {
                return $next($request);
            }

            $deviceId = DeviceIdentity::id();
            $label = DeviceIdentity::label();

            $device = PremiumDevice::query()->firstOrCreate(
                ['device_id' => $deviceId],
                [
                    'label' => $label,
                    'first_seen_at' => now(),
                ]
            );

            $device->forceFill([
                'label' => $label,
                'last_seen_at' => now(),
            ])->save();
        } catch (\Throwable) {
            // Never block requests due to device tracking.
        }

        return $next($request);
    }
}
