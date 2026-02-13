<?php

namespace App\Livewire\Marketplace;

use App\Support\LicenseManager;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Marketplace')]
class Index extends Component
{
    use WithFileUploads;

    public ?string $selectedFeature = null;
    public mixed $licenseFile = null;

    public function selectFeature(string $feature): void
    {
        $feature = trim($feature);
        if (! in_array($feature, ['cbt', 'savings_loan'], true)) {
            return;
        }

        $this->selectedFeature = $feature;
        $this->licenseFile = null;
        $this->resetValidation();
    }

    public function cancelInstall(): void
    {
        $this->selectedFeature = null;
        $this->licenseFile = null;
        $this->resetValidation();
    }

    public function install(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $data = $this->validate([
            'selectedFeature' => ['required', 'string', 'in:cbt,savings_loan'],
            'licenseFile' => ['required', 'file', 'max:64'],
        ]);

        $realPath = $this->licenseFile?->getRealPath();
        $raw = $realPath ? (string) File::get($realPath) : '';

        /** @var \App\Support\LicenseManager $licenses */
        $licenses = app(LicenseManager::class);
        $state = $licenses->verifyRaw($raw);

        if (! ($state['ok'] ?? false)) {
            $this->addError('licenseFile', (string) ($state['reason'] ?? 'Invalid license.'));
            return;
        }

        $features = $state['data']['features'] ?? [];
        if (! is_array($features)) {
            $features = [];
        }

        if (! in_array($data['selectedFeature'], $features, true)) {
            $label = $data['selectedFeature'] === 'cbt' ? 'CBT' : 'Savings/Loan';
            $this->addError('licenseFile', "This license does not enable {$label}.");
            return;
        }

        $installed = $licenses->installRaw($raw);
        if (! ($installed['ok'] ?? false)) {
            $this->addError('licenseFile', (string) ($installed['reason'] ?? 'Invalid license.'));
            return;
        }

        $this->licenseFile = null;
        $this->selectedFeature = null;

        $this->dispatch('alert', message: 'License installed. Premium modules updated.', type: 'success');
    }

    #[Computed]
    public function licenseState(): array
    {
        /** @var \App\Support\LicenseManager $licenses */
        $licenses = app(LicenseManager::class);

        return $licenses->load();
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        return view('livewire.marketplace.index');
    }
}

