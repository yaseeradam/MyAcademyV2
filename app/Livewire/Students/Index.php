<?php

namespace App\Livewire\Students;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('All Students')]
class Index extends Component
{
    use WithPagination;

    public string $classFilter = 'all';
    public string $sectionFilter = 'all';
    public string $statusFilter = 'all';
    public string $search = '';

    #[Computed]
    public function classes()
    {
        return SchoolClass::query()->orderBy('level')->get();
    }

    #[Computed]
    public function sections()
    {
        if ($this->classFilter === 'all') {
            return Section::query()
                ->select('name')
                ->distinct()
                ->orderBy('name')
                ->pluck('name');
        }

        return Section::query()
            ->where('class_id', $this->classFilter)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function students()
    {
        $query = Student::query()->with(['schoolClass', 'section']);

        if ($this->classFilter !== 'all') {
            $query->where('class_id', $this->classFilter);
        }

        if ($this->sectionFilter !== 'all') {
            if ($this->classFilter === 'all') {
                $query->whereHas('section', fn ($q) => $q->where('name', $this->sectionFilter));
            } else {
                $query->where('section_id', $this->sectionFilter);
            }
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%")
                    ->orWhere('guardian_name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('last_name')->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $all = Student::query();

        $total = (clone $all)->count();
        $boys = (clone $all)->where('gender', 'Male')->count();
        $girls = (clone $all)->where('gender', 'Female')->count();

        return [
            'total' => $total,
            'boys' => $boys,
            'girls' => $girls,
            'alumni' => 0,
        ];
    }

    public function updating($name): void
    {
        if (in_array($name, ['classFilter', 'sectionFilter', 'statusFilter', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        return view('livewire.students.index', [
            'students' => $this->students,
        ]);
    }
}
