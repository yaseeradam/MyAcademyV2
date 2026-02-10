<?php

namespace App\Livewire\Promotions;

use App\Models\Promotion;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Promote Students')]
class Index extends Component
{
    public ?int $fromClassId = null;
    public ?int $fromSectionId = null;
    public ?int $toClassId = null;
    public ?int $toSectionId = null;
    public string $note = '';

    /**
     * @var array<int|string>
     */
    public array $selected = [];

    public bool $selectAll = false;

    public function mount(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);
    }

    #[Computed]
    public function classes()
    {
        return SchoolClass::query()->orderBy('level')->get();
    }

    #[Computed]
    public function fromSections()
    {
        if (! $this->fromClassId) {
            return collect();
        }

        return Section::query()->where('class_id', $this->fromClassId)->orderBy('name')->get();
    }

    #[Computed]
    public function toSections()
    {
        if (! $this->toClassId) {
            return collect();
        }

        return Section::query()->where('class_id', $this->toClassId)->orderBy('name')->get();
    }

    #[Computed]
    public function students()
    {
        if (! $this->fromClassId) {
            return collect();
        }

        return Student::query()
            ->where('class_id', $this->fromClassId)
            ->when($this->fromSectionId, fn ($q) => $q->where('section_id', $this->fromSectionId))
            ->where('status', 'Active')
            ->orderBy('last_name')
            ->get();
    }

    public function updatedFromClassId(): void
    {
        $this->fromSectionId = null;
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedFromSectionId(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedToClassId(): void
    {
        $this->toSectionId = null;
    }

    public function toggleSelectAll(): void
    {
        $this->selectAll = ! $this->selectAll;

        if ($this->selectAll) {
            $this->selected = $this->students->pluck('id')->all();
        } else {
            $this->selected = [];
        }
    }

    public function promoteSelected(): void
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        if (! $this->fromClassId || ! $this->toClassId) {
            throw ValidationException::withMessages([
                'fromClassId' => 'Select from class and to class.',
            ]);
        }

        if ($this->toSectionId) {
            $belongs = Section::query()
                ->where('id', $this->toSectionId)
                ->where('class_id', $this->toClassId)
                ->exists();

            if (! $belongs) {
                throw ValidationException::withMessages([
                    'toSectionId' => 'Selected section does not belong to destination class.',
                ]);
            }
        }

        $ids = array_values(array_filter(array_map('intval', $this->selected)));
        $ids = array_values(array_unique(array_filter($ids, fn ($id) => $id > 0)));

        if (count($ids) === 0) {
            throw ValidationException::withMessages([
                'selected' => 'Select at least one student.',
            ]);
        }

        $students = Student::query()->whereIn('id', $ids)->get();
        if ($students->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'selected' => 'Some selected students could not be found.',
            ]);
        }

        $fromClassId = (int) $this->fromClassId;
        $fromSectionId = $this->fromSectionId ? (int) $this->fromSectionId : null;
        $toClassId = (int) $this->toClassId;
        $toSectionId = $this->toSectionId ? (int) $this->toSectionId : null;
        $note = trim($this->note) !== '' ? trim($this->note) : null;

        DB::transaction(function () use ($students, $user, $fromClassId, $fromSectionId, $toClassId, $toSectionId, $note) {
            foreach ($students as $student) {
                if ($student->class_id !== $fromClassId) {
                    throw ValidationException::withMessages([
                        'selected' => 'Selected students must belong to the selected "From class".',
                    ]);
                }

                if ($fromSectionId && $student->section_id !== $fromSectionId) {
                    throw ValidationException::withMessages([
                        'selected' => 'Selected students must belong to the selected "From section".',
                    ]);
                }

                $promotion = Promotion::query()->create([
                    'student_id' => $student->id,
                    'from_class_id' => $student->class_id,
                    'from_section_id' => $student->section_id,
                    'to_class_id' => $toClassId,
                    'to_section_id' => $toSectionId,
                    'promoted_by' => $user->id,
                    'promoted_at' => now(),
                    'note' => $note,
                ]);

                $student->class_id = $toClassId;
                $student->section_id = $toSectionId;
                $student->save();
            }
        });

        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('alert', message: 'Students promoted.', type: 'success');
    }

    public function render()
    {
        $user = auth()->user();
        abort_unless($user?->role === 'admin', 403);

        $recent = Promotion::query()
            ->with(['student:id,first_name,last_name,admission_number', 'fromClass:id,name', 'toClass:id,name'])
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('livewire.promotions.index', [
            'recentPromotions' => $recent,
        ]);
    }
}

