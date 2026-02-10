<?php

namespace App\Livewire\Timetable;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Timetable')]
class Index extends Component
{
    public ?int $classId = null;
    public ?int $sectionId = null;
    public ?int $day = null;

    public ?int $editingId = null;
    public ?int $entryClassId = null;
    public ?int $entrySectionId = null;
    public int $entryDay = 1;
    public string $startsAt = '08:00';
    public string $endsAt = '09:00';
    public ?int $subjectId = null;
    public ?int $teacherId = null;
    public ?string $room = null;

    #[Computed]
    public function classes()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if ($user->role === 'admin') {
            return SchoolClass::query()->orderBy('level')->get();
        }

        $ids = SubjectAllocation::query()->where('teacher_id', $user->id)->pluck('class_id')->unique();

        return SchoolClass::query()
            ->whereIn('id', $ids)
            ->orderBy('level')
            ->get();
    }

    #[Computed]
    public function sections()
    {
        if (! $this->classId) {
            return collect();
        }

        return Section::query()->where('class_id', $this->classId)->orderBy('name')->get();
    }

    #[Computed]
    public function entrySections()
    {
        if (! $this->entryClassId) {
            return collect();
        }

        return Section::query()->where('class_id', $this->entryClassId)->orderBy('name')->get();
    }

    #[Computed]
    public function subjects()
    {
        return Subject::query()->orderBy('name')->get();
    }

    #[Computed]
    public function teachers()
    {
        return User::query()
            ->where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $this->entryDay = (int) now()->isoWeekday();
    }

    public function updatedClassId(): void
    {
        $this->sectionId = null;
    }

    public function updatedEntryClassId(): void
    {
        $this->entrySectionId = null;
    }

    public function clearForm(): void
    {
        $this->editingId = null;
        $this->entryClassId = $this->classId;
        $this->entrySectionId = $this->sectionId;
        $this->entryDay = $this->day ?: (int) now()->isoWeekday();
        $this->startsAt = '08:00';
        $this->endsAt = '09:00';
        $this->subjectId = null;
        $this->teacherId = null;
        $this->room = null;
    }

    public function selectSlot(int $day, string $start, string $end): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $this->clearForm();
        $this->entryDay = $day;
        $this->startsAt = $start;
        $this->endsAt = $end;
    }

    public function edit(int $id): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $e = TimetableEntry::query()->findOrFail($id);

        $this->editingId = $e->id;
        $this->entryClassId = $e->class_id;
        $this->entrySectionId = $e->section_id;
        $this->entryDay = (int) $e->day_of_week;
        $this->startsAt = substr((string) $e->starts_at, 0, 5);
        $this->endsAt = substr((string) $e->ends_at, 0, 5);
        $this->subjectId = $e->subject_id;
        $this->teacherId = $e->teacher_id;
        $this->room = $e->room;
    }

    public function save(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $data = $this->validate([
            'entryClassId' => ['required', 'integer', 'exists:classes,id'],
            'entrySectionId' => ['nullable', 'integer', 'exists:sections,id'],
            'entryDay' => ['required', 'integer', 'between:1,7'],
            'startsAt' => ['required', 'date_format:H:i'],
            'endsAt' => ['required', 'date_format:H:i'],
            'subjectId' => ['required', 'integer', 'exists:subjects,id'],
            'teacherId' => ['nullable', 'integer', 'exists:users,id'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        if ($data['entrySectionId']) {
            $belongs = Section::query()
                ->where('id', $data['entrySectionId'])
                ->where('class_id', $data['entryClassId'])
                ->exists();

            if (! $belongs) {
                throw ValidationException::withMessages([
                    'entrySectionId' => 'Selected section does not belong to the class.',
                ]);
            }
        }

        $startSec = $this->timeToSeconds($data['startsAt']);
        $endSec = $this->timeToSeconds($data['endsAt']);

        if ($endSec <= $startSec) {
            throw ValidationException::withMessages([
                'endsAt' => 'End time must be after start time.',
            ]);
        }

        $this->ensureNoConflicts(
            editingId: $this->editingId,
            classId: (int) $data['entryClassId'],
            sectionId: $data['entrySectionId'] ? (int) $data['entrySectionId'] : null,
            day: (int) $data['entryDay'],
            startSec: $startSec,
            endSec: $endSec,
            teacherId: $data['teacherId'] ? (int) $data['teacherId'] : null
        );

        TimetableEntry::query()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'class_id' => (int) $data['entryClassId'],
                'section_id' => $data['entrySectionId'] ? (int) $data['entrySectionId'] : null,
                'day_of_week' => (int) $data['entryDay'],
                'starts_at' => $data['startsAt'],
                'ends_at' => $data['endsAt'],
                'subject_id' => (int) $data['subjectId'],
                'teacher_id' => $data['teacherId'] ? (int) $data['teacherId'] : null,
                'room' => $data['room'] ? trim($data['room']) : null,
            ]
        );

        $this->dispatch('alert', message: 'Timetable entry saved.', type: 'success');
        $this->clearForm();
    }

    public function delete(int $id): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        TimetableEntry::query()->whereKey($id)->delete();

        if ($this->editingId === $id) {
            $this->clearForm();
        }

        $this->dispatch('alert', message: 'Entry deleted.', type: 'success');
    }

    private function ensureNoConflicts(
        ?int $editingId,
        int $classId,
        ?int $sectionId,
        int $day,
        int $startSec,
        int $endSec,
        ?int $teacherId
    ): void {
        $existing = TimetableEntry::query()
            ->where('class_id', $classId)
            ->where('day_of_week', $day)
            ->get();

        foreach ($existing as $ex) {
            if ($editingId && (int) $ex->id === (int) $editingId) {
                continue;
            }

            if ($sectionId === null) {
                // class-wide entry cannot overlap with any section entry
            } else {
                // section entry overlaps with same section or class-wide entries
                if ($ex->section_id !== null && (int) $ex->section_id !== (int) $sectionId) {
                    continue;
                }
            }

            $exStartSec = $this->timeToSeconds(substr((string) $ex->starts_at, 0, 5));
            $exEndSec = $this->timeToSeconds(substr((string) $ex->ends_at, 0, 5));

            if ($this->overlaps($startSec, $endSec, $exStartSec, $exEndSec)) {
                throw ValidationException::withMessages([
                    'startsAt' => 'Time overlaps an existing class timetable entry.',
                ]);
            }
        }

        if ($teacherId) {
            $teacherEntries = TimetableEntry::query()
                ->where('teacher_id', $teacherId)
                ->where('day_of_week', $day)
                ->get();

            foreach ($teacherEntries as $ex) {
                if ($editingId && (int) $ex->id === (int) $editingId) {
                    continue;
                }

                $exStartSec = $this->timeToSeconds(substr((string) $ex->starts_at, 0, 5));
                $exEndSec = $this->timeToSeconds(substr((string) $ex->ends_at, 0, 5));

                if ($this->overlaps($startSec, $endSec, $exStartSec, $exEndSec)) {
                    throw ValidationException::withMessages([
                        'teacherId' => 'Teacher already has a timetable entry at this time.',
                    ]);
                }
            }
        }
    }

    private function overlaps(int $aStart, int $aEnd, int $bStart, int $bEnd): bool
    {
        return max($aStart, $bStart) < min($aEnd, $bEnd);
    }

    private function timeToSeconds(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time, 2));
        return ($h * 3600) + ($m * 60);
    }

    private function dayLabel(int $day): string
    {
        return match ($day) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => 'Day',
        };
    }

    private function buildTimeSlots($entries): array
    {
        $boundaries = [];

        for ($hour = 8; $hour <= 17; $hour++) {
            $boundaries[] = sprintf('%02d:00', $hour);
        }

        foreach ($entries as $entry) {
            $boundaries[] = substr((string) $entry->starts_at, 0, 5);
            $boundaries[] = substr((string) $entry->ends_at, 0, 5);
        }

        $unique = [];
        foreach ($boundaries as $time) {
            $unique[$this->timeToSeconds($time)] = $time;
        }

        ksort($unique);
        $sortedTimes = array_values($unique);

        $slots = [];
        for ($i = 0; $i < count($sortedTimes) - 1; $i++) {
            $start = $sortedTimes[$i];
            $end = $sortedTimes[$i + 1];
            $slots[] = [
                'key' => $start.'-'.$end,
                'start' => $start,
                'end' => $end,
                'label' => $start.' - '.$end,
                'startSec' => $this->timeToSeconds($start),
                'endSec' => $this->timeToSeconds($end),
            ];
        }

        return $slots;
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user, 403);

        if (! in_array($user->role, ['admin', 'teacher', 'bursar'], true)) {
            abort(403);
        }

        $entries = TimetableEntry::query()
            ->with([
                'schoolClass:id,name',
                'section:id,name',
                'subject:id,name',
                'teacher:id,name',
            ])
            ->when($this->classId, fn ($q) => $q->where('class_id', $this->classId))
            ->when($this->sectionId !== null && $this->sectionId !== 0, fn ($q) => $q->where('section_id', $this->sectionId))
            ->when($this->day, fn ($q) => $q->where('day_of_week', $this->day))
            ->orderBy('day_of_week')
            ->orderBy('starts_at')
            ->get();

        $daysList = $this->day ? [(int) $this->day] : [1, 2, 3, 4, 5, 6, 7];
        $days = collect($daysList)->map(fn ($day) => [
            'day' => $day,
            'label' => $this->dayLabel($day),
        ])->all();

        $timeSlots = $this->buildTimeSlots($entries);

        $gridEntries = $entries;
        if ($this->sectionId === null || (int) $this->sectionId === 0) {
            $gridEntries = $entries->filter(fn ($entry) => $entry->section_id === null);
        }

        $slotMap = [];
        foreach ($gridEntries as $entry) {
            $entryStart = $this->timeToSeconds(substr((string) $entry->starts_at, 0, 5));
            $entryEnd = $this->timeToSeconds(substr((string) $entry->ends_at, 0, 5));

            foreach ($timeSlots as $slot) {
                if ($this->overlaps($entryStart, $entryEnd, $slot['startSec'], $slot['endSec'])) {
                    $slotMap[$entry->day_of_week][$slot['key']] = $entry;
                }
            }
        }

        $grouped = $entries->groupBy('day_of_week')->map(function ($rows, $day) {
            return [
                'day' => (int) $day,
                'label' => $this->dayLabel((int) $day),
                'rows' => $rows,
            ];
        })->sortBy('day')->values();

        return view('livewire.timetable.index', [
            'isAdmin' => $user->role === 'admin',
            'grouped' => $grouped,
            'days' => $days,
            'timeSlots' => $timeSlots,
            'slotMap' => $slotMap,
            'canUseGrid' => (bool) $this->classId,
            'gridUsesSections' => ! ($this->sectionId === null || (int) $this->sectionId === 0),
        ]);
    }
}
