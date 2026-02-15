<?php

namespace App\Livewire\Attendance;

use App\Models\AcademicSession;
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
    public ?int $classId = null;
    public string $date = '';
    public int $term = 1;
    public string $session = '';

    public ?int $sheetId = null;

    /**
     * @var array<int, array{status:string, note:string|null}>
     */
    public array $marks = [];

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->session = $this->session ?: $this->defaultSession();
        $this->term = $this->term ?: $this->defaultTerm();
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
        if (! $this->classId) {
            return collect();
        }

        return Student::query()
            ->where('class_id', $this->classId)
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

    public function updatedClassId(): void
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

    public function start(): void
    {
        $this->validate([
            'classId' => ['required', 'integer', Rule::exists('classes', 'id')],
            'date' => ['required', 'date'],
            'term' => ['required', 'integer', 'between:1,3'],
            'session' => ['required', 'string', 'max:9', 'regex:/^\d{4}\/\d{4}$/'],
        ]);

        $section = $this->sections->first();
        if (! $section) {
            $this->dispatch('alert', message: 'No sections found for this class.', type: 'error');
            return;
        }

        $sheet = AttendanceSheet::query()->firstOrCreate(
            [
                'class_id' => $this->classId,
                'section_id' => $section->id,
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
    }

    public function save(): void
    {
        if (! $this->sheetId) {
            $this->start();
            if (! $this->sheetId) {
                return;
            }
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

        $this->dispatch('alert', message: 'Attendance saved successfully.', type: 'success');
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

    public function cycleStatus(int $studentId): void
    {
        $order = ['Present', 'Absent', 'Late', 'Excused'];
        $current = (string) ($this->marks[$studentId]['status'] ?? 'Present');
        $index = array_search($current, $order, true);
        $next = $order[$index === false ? 0 : ($index + 1) % count($order)];

        $this->marks[$studentId]['status'] = $next;
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
        $active = AcademicSession::activeName();
        if ($active) {
            return $active;
        }

        $year = (int) now()->format('Y');
        $next = $year + 1;

        return "{$year}/{$next}";
    }

    private function defaultTerm(): int
    {
        $term = (string) config('myacademy.current_term', 'Term 1');
        if (preg_match('/(\d)/', $term, $m)) {
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
