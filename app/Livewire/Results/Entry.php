<?php

namespace App\Livewire\Results;

use App\Models\SchoolClass;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Score Entry')]
class Entry extends Component
{
    public ?int $classId = null;
    public ?int $subjectId = null;
    public int $term = 1;
    public string $session = '';

    /**
     * @var array<int, array{ca1:int|null, ca2:int|null, exam:int|null}>
     */
    public array $scores = [];

    public function mount(): void
    {
        $this->session = $this->session ?: $this->defaultSession();
    }

    #[Computed]
    public function classes()
    {
        $user = auth()->user();

        if ($user?->role === 'admin') {
            return SchoolClass::query()->orderBy('level')->get();
        }

        return SchoolClass::query()
            ->whereIn('id', SubjectAllocation::query()->where('teacher_id', $user->id)->pluck('class_id'))
            ->orderBy('level')
            ->get();
    }

    #[Computed]
    public function subjects()
    {
        if (! $this->classId) {
            return collect();
        }

        $user = auth()->user();

        if ($user?->role === 'admin') {
            $ids = SubjectAllocation::query()->where('class_id', $this->classId)->pluck('subject_id')->unique();
            return $ids->isNotEmpty()
                ? Subject::query()->whereIn('id', $ids)->orderBy('name')->get()
                : Subject::query()->orderBy('name')->get();
        }

        $ids = SubjectAllocation::query()
            ->where('class_id', $this->classId)
            ->where('teacher_id', $user->id)
            ->pluck('subject_id')
            ->unique();

        return Subject::query()->whereIn('id', $ids)->orderBy('name')->get();
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

    public function updatedClassId(): void
    {
        $this->subjectId = null;
        $this->scores = [];
    }

    public function updatedSubjectId(): void
    {
        $this->loadExistingScores();
    }

    public function updatedTerm(): void
    {
        $this->loadExistingScores();
    }

    public function updatedSession(): void
    {
        $this->loadExistingScores();
    }

    public function save(): void
    {
        if (! $this->classId || ! $this->subjectId) {
            throw ValidationException::withMessages([
                'classId' => 'Select a class and subject.',
            ]);
        }

        $user = auth()->user();
        if ($user?->role !== 'admin') {
            $allowed = SubjectAllocation::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $this->classId)
                ->where('subject_id', $this->subjectId)
                ->exists();

            abort_unless($allowed, 403);
        }

        $this->validate([
            'term' => ['required', 'integer', 'between:1,3'],
            'session' => ['required', 'string', 'max:9'],
        ]);

        DB::transaction(function () {
            foreach ($this->students as $student) {
                $row = $this->scores[$student->id] ?? [];

                $ca1 = (int) ($row['ca1'] ?? 0);
                $ca2 = (int) ($row['ca2'] ?? 0);
                $exam = (int) ($row['exam'] ?? 0);

                if ($ca1 < 0 || $ca1 > 20 || $ca2 < 0 || $ca2 > 20 || $exam < 0 || $exam > 60) {
                    throw ValidationException::withMessages([
                        'scores' => "Invalid score range for {$student->full_name}.",
                    ]);
                }

                Score::query()->updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $this->subjectId,
                        'class_id' => $this->classId,
                        'term' => $this->term,
                        'session' => $this->session,
                    ],
                    [
                        'ca1' => $ca1,
                        'ca2' => $ca2,
                        'exam' => $exam,
                    ]
                );
            }
        });

        $this->dispatch('alert', message: 'Scores saved.', type: 'success');
        $this->loadExistingScores();
    }

    private function loadExistingScores(): void
    {
        $this->scores = [];

        if (! $this->classId || ! $this->subjectId || ! $this->session) {
            return;
        }

        $existing = Score::query()
            ->where('class_id', $this->classId)
            ->where('subject_id', $this->subjectId)
            ->where('term', $this->term)
            ->where('session', $this->session)
            ->get()
            ->keyBy('student_id');

        foreach ($this->students as $student) {
            $score = $existing->get($student->id);
            $this->scores[$student->id] = [
                'ca1' => $score?->ca1 ?? 0,
                'ca2' => $score?->ca2 ?? 0,
                'exam' => $score?->exam ?? 0,
            ];
        }
    }

    private function defaultSession(): string
    {
        $year = (int) now()->format('Y');
        $next = $year + 1;

        return "{$year}/{$next}";
    }

    public function render()
    {
        return view('livewire.results.entry');
    }
}
