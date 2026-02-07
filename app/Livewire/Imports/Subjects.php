<?php

namespace App\Livewire\Imports;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Import Subjects')]
class Subjects extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $file = null;
    public bool $updateExisting = false;

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
                $existing = Subject::query()->where('code', $row['code'])->first();
                if ($existing) {
                    if ($this->updateExisting) {
                        $existing->name = $row['name'];
                        $existing->save();
                    }
                    continue;
                }

                Subject::query()->create($row);
            }
        });

        $this->dispatch('alert', message: 'Subjects imported.', type: 'success');
    }

    /**
     * @return array{0:array,1:array,2?:array<int,array{code:string,name:string}>}
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

        foreach (['code', 'name'] as $col) {
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
            $code = strtoupper(trim((string) ($data[$map['code']] ?? '')));
            $name = trim((string) ($data[$map['name']] ?? ''));

            if ($code === '' || $name === '') {
                $errors[] = "Line {$line}: code and name are required.";
                continue;
            }

            $rows[] = ['code' => $code, 'name' => $name];
        }

        fclose($handle);

        $codes = collect($rows)->pluck('code')->unique();
        $existing = $codes->isEmpty()
            ? collect()
            : Subject::query()->whereIn('code', $codes)->pluck('code');

        $summary = [
            'rows_valid' => count($rows),
            'to_create' => max(0, $codes->count() - $existing->count()),
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
        return view('livewire.imports.subjects');
    }
}
