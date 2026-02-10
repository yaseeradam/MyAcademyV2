<?php

namespace App\Livewire\Academics;

use App\Models\AcademicSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Academic Sessions')]
class Sessions extends Component
{
    public ?int $editingId = null;
    public string $name = '';
    public ?string $startsOn = null;
    public ?string $endsOn = null;
    public bool $isActive = false;

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);
    }

    public function edit(int $id): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $s = AcademicSession::query()->findOrFail($id);
        $this->editingId = $s->id;
        $this->name = $s->name;
        $this->startsOn = $s->starts_on?->toDateString();
        $this->endsOn = $s->ends_on?->toDateString();
        $this->isActive = (bool) $s->is_active;
    }

    public function clearForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->startsOn = null;
        $this->endsOn = null;
        $this->isActive = false;
    }

    public function save(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $data = $this->validate([
            'name' => ['required', 'string', 'regex:/^\\d{4}\\/\\d{4}$/', 'max:9'],
            'startsOn' => ['nullable', 'date'],
            'endsOn' => ['nullable', 'date'],
            'isActive' => ['boolean'],
        ]);

        [$y1, $y2] = explode('/', $data['name'], 2);
        if ((int) $y2 !== (int) $y1 + 1) {
            throw ValidationException::withMessages([
                'name' => 'Session should look like 2026/2027.',
            ]);
        }

        if ($data['startsOn'] && $data['endsOn'] && $data['endsOn'] < $data['startsOn']) {
            throw ValidationException::withMessages([
                'endsOn' => 'End date must be after start date.',
            ]);
        }

        DB::transaction(function () use ($data) {
            if ($data['isActive']) {
                AcademicSession::query()->where('is_active', true)->update(['is_active' => false]);
            }

            AcademicSession::query()->updateOrCreate(
                ['id' => $this->editingId],
                [
                    'name' => $data['name'],
                    'starts_on' => $data['startsOn'] ?: null,
                    'ends_on' => $data['endsOn'] ?: null,
                    'is_active' => (bool) $data['isActive'],
                ]
            );
        });

        $this->dispatch('alert', message: 'Academic session saved.', type: 'success');
        $this->clearForm();
    }

    public function setActive(int $id): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        DB::transaction(function () use ($id) {
            AcademicSession::query()->where('is_active', true)->update(['is_active' => false]);
            AcademicSession::query()->whereKey($id)->update(['is_active' => true]);
        });

        $this->dispatch('alert', message: 'Active session updated.', type: 'success');
    }

    public function delete(int $id): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        AcademicSession::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->clearForm();
        }

        $this->dispatch('alert', message: 'Session deleted.', type: 'success');
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $sessions = AcademicSession::query()->orderByDesc('is_active')->orderByDesc('id')->get();

        return view('livewire.academics.sessions', [
            'sessions' => $sessions,
        ]);
    }
}

