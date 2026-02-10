<?php

namespace App\Livewire\Premium;

use App\Models\PremiumDevice;
use App\Models\PremiumDeviceRemoval;
use App\Support\DeviceIdentity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Premium Devices')]
class Devices extends Component
{
    #[Computed]
    public function currentDeviceId(): string
    {
        return DeviceIdentity::id();
    }

    #[Computed]
    public function removalLimit(): int
    {
        return (int) config('myacademy.premium_device_removal_limit', 2);
    }

    #[Computed]
    public function removalWindowDays(): int
    {
        return (int) config('myacademy.premium_device_removal_window_days', 30);
    }

    #[Computed]
    public function removalsInWindow(): int
    {
        $cutoff = now()->subDays($this->removalWindowDays);

        return PremiumDeviceRemoval::query()
            ->where('created_at', '>=', $cutoff)
            ->count();
    }

    #[Computed]
    public function nextRemovalResetAt(): ?Carbon
    {
        $cutoff = now()->subDays($this->removalWindowDays);

        $oldest = PremiumDeviceRemoval::query()
            ->where('created_at', '>=', $cutoff)
            ->orderBy('created_at')
            ->value('created_at');

        return $oldest ? Carbon::parse($oldest)->addDays($this->removalWindowDays) : null;
    }

    #[Computed]
    public function devices()
    {
        return PremiumDevice::query()
            ->orderByRaw('revoked_at is null desc')
            ->orderByDesc('last_seen_at')
            ->orderByDesc('id')
            ->get();
    }

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);
        abort_unless($user->role === 'admin', 403);
    }

    public function removeDevice(int $deviceId): void
    {
        $user = auth()->user();
        abort_unless($user, 403);
        abort_unless($user->role === 'admin', 403);

        $device = PremiumDevice::query()->findOrFail($deviceId);

        if ($device->revoked_at) {
            return;
        }

        if ($device->device_id === $this->currentDeviceId) {
            throw ValidationException::withMessages([
                'device' => 'You cannot remove the current device.',
            ]);
        }

        if ($this->removalsInWindow >= $this->removalLimit) {
            $resetAt = $this->nextRemovalResetAt?->format('M j, Y g:i A');

            throw ValidationException::withMessages([
                'device' => $resetAt
                    ? "Device removals limit reached. Try again after {$resetAt}."
                    : 'Device removals limit reached.',
            ]);
        }

        DB::transaction(function () use ($device, $user) {
            $device->forceFill([
                'revoked_at' => now(),
                'revoked_by_user_id' => $user->id,
            ])->save();

            PremiumDeviceRemoval::query()->create([
                'premium_device_id' => $device->id,
                'removed_by_user_id' => $user->id,
            ]);
        });

        $this->dispatch('alert', message: 'Device removed.', type: 'success');
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user, 403);
        abort_unless($user->role === 'admin', 403);

        return view('livewire.premium.devices');
    }
}

