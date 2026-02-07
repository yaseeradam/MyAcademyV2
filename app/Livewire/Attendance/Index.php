<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceMark;
use App\Models\AttendanceSheet;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Attendance')]
class Index extends Component
{
    public string $tab = 'take';

    public ?int $classId = null;
    public ?int $sectionId = null;
    public string $date = '';
    public int $term = 1;
    public string $session = '';

    public ?int $sheetId = null;

    /**
     * @var array<int, array{status:string, note:string|null}>
     */
    public array $marks = [];

    public ?int $historyClassId = null;
    public ?int $historySectionId = null;
    public ?string $historyFrom = null;
    public ?string $historyTo = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->session = $this->session ?: $this->defaultSession();
        $this->term = $this->term ?: $this->defaultTerm();

        $this->historyFrom = now()->subDays(14)->toDateString();
        $this->historyTo = now()->toDateString();

        $sheetId = (int) request('sheet', 0);
        if ($sheetId > 0) {
            $this->openSheet($sheetId);
        }
    }

    #[Computed]
    public function classes()
    {
        return SchoolClass::query()->orderBy('level')->get();
    }

    #[Computed]
    public function sections()
    {
        if (! $this->classId) {
            return collect();
        }

        return Section::query()
            ->where('class_id', $this->classId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function students()
    {
        if (! $this->classId || ! $this->sectionId) {
            return collect();
        }

        return Student::query()
            ->where('class_id', $this->classId)
            ->where('section_id', $this->sectionId)
            ->where('status', 'Active')
            ->orderBy('last_name')
            ->get();
    }

    #[Computed]
    public function markCounts(): array
    {
        $counts = [
            'Present' => 0,
            'Absent' => 0,
            'Late' => 0,
            'Excused' => 0,
        ];

        foreach ($this->students as $student) {
            $status = (string) ($this->marks[$student->id]['status'] ?? 'Present');
            if (! array_key_exists($status, $counts)) {
                $status = 'Present';
            }

            $counts[$status]++;
        }

        return $counts;
    }

    #[Computed]
    public function historyClasses()
    {
        return $this->classes;
    }

    #[Computed]
    public function historySections()
    {
        if (! $this->historyClassId) {
            return collect();
        }

        return Section::query()
            ->where('class_id', $this->historyClassId)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function historySheets()
    {
        $query = AttendanceSheet::query()
            ->with(['schoolClass', 'section', 'takenBy'])
            ->withCount([
                'marks as present_count' => fn ($q) => $q->where('status', 'Present'),
                'marks as absent_count' => fn ($q) => $q->where('status', 'Absent'),
                'marks as late_count' => fn ($q) => $q->where('status', 'Late'),
                'marks as excused_count' => fn ($q) => $q->where('status', 'Excused'),
            ])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($this->historyClassId) {
            $query->where('class_id', $this->historyClassId);
        }

        if ($this->historySectionId) {
            $query->where('section_id', $this->historySectionId);
        }

        if ($this->historyFrom) {
            $query->whereDate('date', '>=', $this->historyFrom);
        }

        if ($this->historyTo) {
            $query->whereDate('date', '<=', $this->historyTo);
        }

        return $query->limit(30)->get();
    }

    public function updatedClassId(): void
    {
        $this->sectionId = null;
        $this->resetSheet();
    }

    public function updatedSectionId(): void
    {
        $this->resetSheet();
    }

    public function updatedDate(): void
    {
        $this->resetSheet();
    }

    public function updatedTerm(): void
    {
        $this->resetSheet();
    }

    public function updatedSession(): void
    {
        $this->resetSheet();
    }

    public function updatedHistoryClassId(): void
    {
        $this->historySectionId = null;
    }

    public function start(): void
    {
        $this->validateSheetSelector();

        $sheet = AttendanceSheet::query()->firstOrCreate(
            [
                'class_id' => $this->classId,
                'section_id' => $this->sectionId,
                'date' => $this->date,
                'term' => $this->term,
                'session' => $this->session,
            ],
            [
                'taken_by' => auth()->id(),
            ],
        );

        $this->sheetId = $sheet->id;
        $this->loadMarks($sheet);
        $this->tab = 'take';
    }

    public function save(): void
    {
        $this->validateSheetSelector();

        if (! $this->sheetId) {
            $this->start();
        }

        $sheet = AttendanceSheet::query()->findOrFail($this->sheetId);

        DB::transaction(function () use ($sheet) {
            foreach ($this->students as $student) {
                $row = $this->marks[$student->id] ?? [];
                $status = (string) ($row['status'] ?? 'Present');
                $note = $row['note'] ?? null;

                if (! in_array($status, ['Present', 'Absent', 'Late', 'Excused'], true)) {
                    throw ValidationException::withMessages([
                        'marks' => "Invalid attendance status for {$student->full_name}.",
                    ]);
                }

                AttendanceMark::query()->updateOrCreate(
                    [
                        'sheet_id' => $sheet->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => $status,
                        'note' => $note ? (string) $note : null,
                    ],
                );
            }
        });

        $this->dispatch('alert', message: 'Attendance saved.', type: 'success');
    }

    public function openSheet(int $sheetId): void
    {
        $sheet = AttendanceSheet::query()->findOrFail($sheetId);

        $this->classId = (int) $sheet->class_id;
        $this->sectionId = (int) $sheet->section_id;
        $this->date = $sheet->date->toDateString();
        $this->term = (int) $sheet->term;
        $this->session = (string) $sheet->session;
        $this->sheetId = (int) $sheet->id;

        $this->loadMarks($sheet);
        $this->tab = 'take';
    }

    public function viewHistory(): void
    {
        $this->tab = 'history';
    }

    public function markAll(string $status): void
    {
        if (! in_array($status, ['Present', 'Absent', 'Late', 'Excused'], true)) {
            return;
        }

        foreach ($this->students as $student) {
            $this->marks[$student->id]['status'] = $status;
        }
    }

    private function validateSheetSelector(): void
    {
        $this->validate([
            'classId' => ['required', 'integer', Rule::exists('classes', 'id')],
            'sectionId' => ['required', 'integer', Rule::exists('sections', 'id')],
            'date' => ['required', 'date'],
            'term' => ['required', 'integer', 'between:1,3'],
            'session' => ['required', 'string', 'max:9', 'regex:/^\\d{4}\\/\\d{4}$/'],
        ]);
    }

    private function loadMarks(AttendanceSheet $sheet): void
    {
        $this->marks = [];

        $existing = AttendanceMark::query()
            ->where('sheet_id', $sheet->id)
            ->get()
            ->keyBy('student_id');

        foreach ($this->students as $student) {
            $mark = $existing->get($student->id);
            $this->marks[$student->id] = [
                'status' => $mark?->status ?? 'Present',
                'note' => $mark?->note,
            ];
        }
    }

    private function resetSheet(): void
    {
        $this->sheetId = null;
        $this->marks = [];
    }

    private function defaultSession(): string
    {
        $year = (int) now()->format('Y');
        $next = $year + 1;

        return "{$year}/{$next}";
    }

    private function defaultTerm(): int
    {
        $term = (string) config('myacademy.current_term', 'Term 1');
        if (preg_match('/(\\d)/', $term, $m)) {
            $n = (int) $m[1];
            if ($n >= 1 && $n <= 3) {
                return $n;
            }
        }

        return 1;
    }

    public function render()
    {
        return view('livewire.attendance.index');
    }
}
