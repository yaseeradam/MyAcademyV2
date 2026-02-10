<?php

namespace Tests\Feature;

use App\Livewire\Premium\Devices as PremiumDevices;
use App\Models\PremiumDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class PremiumDevicesRemovalLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_removal_is_limited_to_two_in_rolling_thirty_days(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-10 10:00:00'));

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $d1 = PremiumDevice::query()->create(['device_id' => 'dev-1', 'label' => 'Device 1']);
        $d2 = PremiumDevice::query()->create(['device_id' => 'dev-2', 'label' => 'Device 2']);
        $d3 = PremiumDevice::query()->create(['device_id' => 'dev-3', 'label' => 'Device 3']);

        Livewire::actingAs($admin)
            ->test(PremiumDevices::class)
            ->call('removeDevice', $d1->id)
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(PremiumDevices::class)
            ->call('removeDevice', $d2->id)
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(PremiumDevices::class)
            ->call('removeDevice', $d3->id)
            ->assertHasErrors(['device']);

        Carbon::setTestNow(Carbon::parse('2026-03-13 10:00:00'));

        Livewire::actingAs($admin)
            ->test(PremiumDevices::class)
            ->call('removeDevice', $d3->id)
            ->assertHasNoErrors();
    }
}

