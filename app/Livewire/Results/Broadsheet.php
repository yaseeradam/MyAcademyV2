<?php

namespace App\Livewire\Results;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Broadsheet')]
class Broadsheet extends Component
{
    public ?int $classId = null;
    public int $term = 1;
    public string $session = '';

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

        $ids = SubjectAllocation::query()
            ->where('class_id', $this->classId)
            ->pluck('subject_id')
            ->unique();

        if ($ids->isEmpty()) {
            return Subject::query()->orderBy('name')->get();
        }

        return Subject::query()->whereIn('id', $ids)->orderBy('name')->get();
    }

    #[Computed]
    public function rows(): Collection
    {
        if (! $this->classId || ! $this->session) {
            return collect();
        }

        $students = Student::query()
            ->where('class_id', $this->classId)
            ->where('status', 'Active')
            ->orderBy('last_name')
            ->get();

        $subjectIds = $this->subjects->pluck('id');

        $scores = Score::query()
            ->where('class_id', $this->classId)
            ->where('term', $this->term)
            ->where('session', $this->session)
            ->whereIn('subject_id', $subjectIds)
            ->get();

        $byStudent = $scores
            ->groupBy('student_id')
            ->map(fn ($rows) => $rows->keyBy('subject_id'));

        $subjectCount = max(1, (int) $subjectIds->count());

        $rows = $students->map(function (Student $student) use ($subjectIds, $byStudent, $subjectCount) {
            $map = $byStudent->get($student->id, collect());
            $subjectTotals = [];
            $grandTotal = 0;

            foreach ($subjectIds as $subjectId) {
                /** @var \App\Models\Score|null $score */
                $score = $map->get($subjectId);
                $subjectTotals[$subjectId] = $score?->total ?? null;
                $grandTotal += (int) ($score?->total ?? 0);
            }

            $average = round($grandTotal / $subjectCount, 2);

            return [
                'student' => $student,
                'subjectTotals' => $subjectTotals,
                'grandTotal' => $grandTotal,
                'average' => $average,
            ];
        });

        $sorted = $rows->sortByDesc('grandTotal')->values();

        $rank = 0;
        $lastScore = null;
        $sorted = $sorted->map(function (array $row, int $i) use (&$rank, &$lastScore) {
            if ($lastScore === null || $row['grandTotal'] !== $lastScore) {
                $rank = $i + 1;
                $lastScore = $row['grandTotal'];
            }

            $row['position'] = $rank;

            return $row;
        });

        return $sorted;
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
        return view('livewire.results.broadsheet');
    }
}
