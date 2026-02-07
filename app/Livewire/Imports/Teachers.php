<?php

namespace App\Livewire\Imports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Import Teachers')]
class Teachers extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $file = null;
    public bool $updateExisting = false;
    public bool $defaultActive = true;

    public array $summary = [];
    public array $errorsPreview = [];

    public function analyze(): void
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        [$summary, $errors] = $this->parseCsv();
        $this->summary = $summary;
        $this->errorsPreview = array_slice($errors, 0, 10);
    }

    public function import(): void
    {
        [$summary, $errors, $rows] = $this->parseCsv(returnRows: true);
        if ($errors !== []) {
            throw ValidationException::withMessages(['file' => 'Fix CSV errors before importing.']);
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $existing = User::query()->where('email', $row['email'])->first();
                if ($existing) {
                    if ($this->updateExisting) {
                        $existing->name = $row['name'];
                        $existing->role = 'teacher';
                        $existing->is_active = $row['is_active'];
                        if ($row['password']) {
                            $existing->password = $row['password'];
                        }
                        $existing->save();
                    }
                    continue;
                }

                User::query()->create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'role' => 'teacher',
                    'is_active' => $row['is_active'],
                    'password' => $row['password'] ?: Str::password(12),
                ]);
            }
        });

        $this->dispatch('alert', message: 'Teachers imported.', type: 'success');
    }

    /**
     * @return array{0:array,1:array,2?:array<int,array{name:string,email:string,password:string,is_active:bool}>}
     */
    private function parseCsv(bool $returnRows = false): array
    {
        $path = $this->file?->getRealPath();
        if (! $path) {
            throw ValidationException::withMessages(['file' => 'Invalid upload.']);
        }

        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw ValidationException::withMessages(['file' => 'Unable to read file.']);
        }

        $headers = fgetcsv($handle);
        if (! is_array($headers)) {
            fclose($handle);
            throw ValidationException::withMessages(['file' => 'CSV has no header row.']);
        }

        $headers = array_map(fn ($h) => Str::of((string) $h)->trim()->lower()->toString(), $headers);
        $map = array_flip($headers);

        foreach (['name', 'email'] as $col) {
            if (! array_key_exists($col, $map)) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Missing column: {$col}"]);
            }
        }

        $errors = [];
        $rows = [];
        $line = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            $name = trim((string) ($data[$map['name']] ?? ''));
            $email = strtolower(trim((string) ($data[$map['email']] ?? '')));
            $password = array_key_exists('password', $map) ? trim((string) ($data[$map['password']] ?? '')) : '';
            $isActive = array_key_exists('is_active', $map)
                ? filter_var($data[$map['is_active']] ?? '', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : null;

            if ($name === '' || $email === '') {
                $errors[] = "Line {$line}: name and email are required.";
                continue;
            }

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $errors[] = "Line {$line}: invalid email ({$email}).";
                continue;
            }

            if ($password !== '' && strlen($password) < 8) {
                $errors[] = "Line {$line}: password must be at least 8 characters.";
                continue;
            }

            $rows[] = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'is_active' => $isActive ?? $this->defaultActive,
            ];
        }

        fclose($handle);

        $emails = collect($rows)->pluck('email')->unique();
        $existing = $emails->isEmpty()
            ? collect()
            : User::query()->whereIn('email', $emails)->pluck('email');

        $summary = [
            'rows_valid' => count($rows),
            'to_create' => max(0, $emails->count() - $existing->count()),
            'to_update' => $this->updateExisting ? $existing->count() : 0,
            'to_skip_existing' => $this->updateExisting ? 0 : $existing->count(),
            'errors' => count($errors),
        ];

        if ($returnRows) {
            return [$summary, $errors, $rows];
        }

        return [$summary, $errors];
    }

    public function render()
    {
        return view('livewire.imports.teachers');
    }
}
