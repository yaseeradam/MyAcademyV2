<?php

namespace App\Livewire\Results;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAllocation;
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

    /**
     * @return array{ca1:int, ca2:int, exam:int}
     */
    public function maxMarks(): array
    {
        return [
            'ca1' => max(0, (int) config('myacademy.results_ca1_max', 20)),
            'ca2' => max(0, (int) config('myacademy.results_ca2_max', 20)),
            'exam' => max(0, (int) config('myacademy.results_exam_max', 60)),
        ];
    }

    public function mount(): void
    {
        $this->session = $this->session ?: $this->defaultSession();

        $user = auth()->user();
        $defaultClassId = (int) request('class', 0);
        $defaultSubjectId = (int) request('subject', 0);
        $defaultTerm = (int) request('term', 0);
        $defaultSession = trim((string) request('session', ''));

        if ($user?->role === 'teacher' && $defaultClassId > 0) {
            $allowedClass = SubjectAllocation::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $defaultClassId)
                ->exists();

            if (! $allowedClass) {
                $defaultClassId = 0;
                $defaultSubjectId = 0;
            }
        }

        if ($user?->role === 'teacher' && $defaultClassId > 0 && $defaultSubjectId > 0) {
            $allowedSubject = SubjectAllocation::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $defaultClassId)
                ->where('subject_id', $defaultSubjectId)
                ->exists();

            if (! $allowedSubject) {
                $defaultSubjectId = 0;
            }
        }

        if ($defaultClassId > 0) {
            $this->classId = $defaultClassId;
        }

        if ($defaultSubjectId > 0) {
            $this->subjectId = $defaultSubjectId;
        }

        if ($defaultTerm >= 1 && $defaultTerm <= 3) {
            $this->term = $defaultTerm;
        }

        if ($defaultSession !== '') {
            $this->session = $defaultSession;
        }

        if ($this->classId && $this->subjectId) {
            $this->loadExistingScores();
        }
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

        $user = auth()->user();
        if ($user?->role === 'teacher') {
            $allowed = SubjectAllocation::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $this->classId)
                ->exists();

            if (! $allowed) {
                return collect();
            }
        }

        return Student::query()
            ->where('class_id', $this->classId)
            ->where('status', 'Active')
            ->orderBy('last_name')
            ->get();
    }

    #[Computed]
    public function submission()
    {
        return null;
    }

    #[Computed]
    public function submissions()
    {
        return collect();
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

    public function updatedScores(mixed $value, ?string $name = null): void
    {
        if ($name === null || $name === '') {
            return;
        }

        $parts = explode('.', $name);
        if (count($parts) < 2) {
            return;
        }

        if (! is_numeric($parts[0]) && isset($parts[1]) && is_numeric($parts[1])) {
            array_shift($parts);
        }

        if (! isset($parts[0])) {
            return;
        }

        $studentId = (int) $parts[0];
        $field = (string) end($parts);

        $maxMarks = $this->maxMarks();
        if (! array_key_exists($field, $maxMarks)) {
            return;
        }

        if ($value === '' || $value === null) {
            $value = 0;
        }

        $score = (int) $value;

        if ($score < 0) {
            $this->scores[$studentId][$field] = 0;
            $this->dispatch('alert', message: 'Scores cannot be negative.', type: 'warning');
            return;
        }

        $max = $maxMarks[$field];
        if ($score > $max) {
            $label = strtoupper($field);
            $this->scores[$studentId][$field] = $max;
            $this->dispatch('alert', message: "{$label} max is {$max}.", type: 'warning');
        }
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

        $maxMarks = $this->maxMarks();
        $maxTotal = $maxMarks['ca1'] + $maxMarks['ca2'] + $maxMarks['exam'];
        if ($maxTotal <= 0) {
            throw ValidationException::withMessages([
                'scores' => 'Invalid scoring settings. Please update max marks in Settings.',
            ]);
        }

        DB::transaction(function () use ($maxMarks) {
            foreach ($this->students as $student) {
                $row = $this->scores[$student->id] ?? [];

                $ca1 = (int) ($row['ca1'] ?? 0);
                $ca2 = (int) ($row['ca2'] ?? 0);
                $exam = (int) ($row['exam'] ?? 0);

                if (
                    $ca1 < 0 || $ca1 > $maxMarks['ca1']
                    || $ca2 < 0 || $ca2 > $maxMarks['ca2']
                    || $exam < 0 || $exam > $maxMarks['exam']
                ) {
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

    public function submitScores(): void
    {
        $this->save();
        $this->dispatch('alert', message: 'Scores saved successfully.', type: 'success');
    }

    public function approveSubmission(int $id): void
    {
        $this->dispatch('alert', message: 'Feature not available.', type: 'info');
    }

    public function startReject(int $id): void
    {
        $this->dispatch('alert', message: 'Feature not available.', type: 'info');
    }

    public function confirmReject(): void
    {
        $this->dispatch('alert', message: 'Feature not available.', type: 'info');
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
        $active = AcademicSession::activeName();
        if ($active) {
            return $active;
        }

        $year = (int) now()->format('Y');
        $next = $year + 1;

        return "{$year}/{$next}";
    }

    public function render()
    {
        return view('livewire.results.entry');
    }
}
