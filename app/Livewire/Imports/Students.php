<?php

namespace App\Livewire\Imports;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Import Students')]
class Students extends Component
{
    use WithFileUploads;

    public ?TemporaryUploadedFile $file = null;

    public bool $createMissingClasses = false;
    public bool $createMissingSections = false;
    public bool $updateExisting = false;

    public array $summary = [];
    public array $errorsPreview = [];

    public function updatedFile(): void
    {
        $this->summary = [];
        $this->errorsPreview = [];
    }

    public function analyze(): void
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        [$summary, $errors] = $this->parseCsv(dryRun: true);
        $this->summary = $summary;
        $this->errorsPreview = array_slice($errors, 0, 10);
    }

    public function import(): void
    {
        $this->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        [$summary, $errors, $rows] = $this->parseCsv(dryRun: false, returnRows: true);
        if ($errors !== []) {
            throw ValidationException::withMessages([
                'file' => 'Fix CSV errors before importing.',
            ]);
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $class = $this->resolveClass($row['class_name']);
                $section = $this->resolveSection($class->id, $row['section_name']);

                $payload = [
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'class_id' => (int) $class->id,
                    'section_id' => (int) $section->id,
                    'gender' => $row['gender'],
                    'dob' => $row['dob'] ?: null,
                    'blood_group' => $row['blood_group'] ?: null,
                    'guardian_name' => $row['guardian_name'] ?: null,
                    'guardian_phone' => $row['guardian_phone'] ?: null,
                    'guardian_address' => $row['guardian_address'] ?: null,
                    'status' => $row['status'] ?: 'Active',
                ];

                $existing = Student::query()->where('admission_number', $row['admission_number'])->first();
                if ($existing) {
                    if ($this->updateExisting) {
                        $existing->fill($payload)->save();
                    }
                    continue;
                }

                Student::query()->create([
                    'admission_number' => $row['admission_number'],
                ] + $payload);
            }
        });

        $this->dispatch('alert', message: 'Students imported.', type: 'success');
    }

    /**
     * @return array{0:array,1:array,2?:array<int,array<string,string>>}
     */
    private function parseCsv(bool $dryRun, bool $returnRows = false): array
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

        $required = [
            'admission_number',
            'first_name',
            'last_name',
            'gender',
            'class_name',
            'section_name',
        ];

        foreach ($required as $col) {
            if (! array_key_exists($col, $map)) {
                fclose($handle);
                throw ValidationException::withMessages(['file' => "Missing column: {$col}"]);
            }
        }

        $optional = [
            'dob',
            'blood_group',
            'guardian_name',
            'guardian_phone',
            'guardian_address',
            'status',
        ];

        $errors = [];
        $rows = [];
        $line = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $line++;
            if (! is_array($data) || $data === []) {
                continue;
            }

            $get = fn (string $key) => isset($map[$key]) ? trim((string) ($data[$map[$key]] ?? '')) : '';
            $row = [
                'admission_number' => strtoupper($get('admission_number')),
                'first_name' => $get('first_name'),
                'last_name' => $get('last_name'),
                'gender' => ucfirst(strtolower($get('gender'))),
                'class_name' => $get('class_name'),
                'section_name' => strtoupper($get('section_name')),
            ];

            foreach ($optional as $col) {
                $row[$col] = $get($col);
            }

            if ($row['admission_number'] === '' || $row['first_name'] === '' || $row['last_name'] === '' || $row['class_name'] === '' || $row['section_name'] === '') {
                $errors[] = "Line {$line}: missing required fields.";
                continue;
            }

            if (! in_array($row['gender'], ['Male', 'Female'], true)) {
                $errors[] = "Line {$line}: gender must be Male or Female.";
                continue;
            }

            if ($row['status'] !== '' && ! in_array($row['status'], ['Active', 'Graduated', 'Expelled'], true)) {
                $errors[] = "Line {$line}: invalid status.";
                continue;
            }

            if ($dryRun) {
                // Just validate existence unless creation is allowed.
                $class = SchoolClass::query()->where('name', $row['class_name'])->first();
                if (! $class && ! $this->createMissingClasses) {
                    $errors[] = "Line {$line}: class not found ({$row['class_name']}).";
                    continue;
                }

                if ($class) {
                    $section = Section::query()
                        ->where('class_id', $class->id)
                        ->where('name', $row['section_name'])
                        ->first();

                    if (! $section && ! $this->createMissingSections) {
                        $errors[] = "Line {$line}: section not found ({$row['section_name']}) for class {$row['class_name']}.";
                        continue;
                    }
                }
            }

            $rows[] = $row;
        }

        fclose($handle);

        $adms = collect($rows)->pluck('admission_number')->filter()->unique()->values();
        $existing = $adms->isEmpty()
            ? collect()
            : Student::query()->whereIn('admission_number', $adms)->pluck('admission_number');

        $toUpdate = $existing->count();
        $toCreate = max(0, $adms->count() - $toUpdate);

        $summary = [
            'rows_valid' => count($rows),
            'to_create' => $toCreate,
            'to_update' => $this->updateExisting ? $toUpdate : 0,
            'to_skip_existing' => $this->updateExisting ? 0 : $toUpdate,
            'errors' => count($errors),
        ];

        if ($returnRows) {
            return [$summary, $errors, $rows];
        }

        return [$summary, $errors];
    }

    private function resolveClass(string $name): SchoolClass
    {
        $class = SchoolClass::query()->where('name', $name)->first();
        if ($class) {
            return $class;
        }

        if (! $this->createMissingClasses) {
            throw ValidationException::withMessages(['file' => "Class not found: {$name}"]);
        }

        return SchoolClass::query()->create([
            'name' => $name,
            'level' => 1,
        ]);
    }

    private function resolveSection(int $classId, string $name): Section
    {
        $section = Section::query()->where('class_id', $classId)->where('name', $name)->first();
        if ($section) {
            return $section;
        }

        if (! $this->createMissingSections) {
            throw ValidationException::withMessages(['file' => "Section not found: {$name}"]);
        }

        return Section::query()->create([
            'class_id' => $classId,
            'name' => $name,
        ]);
    }

    public function render()
    {
        return view('livewire.imports.students');
    }
}
