<?php

namespace App\Livewire\Classes;

use App\Models\SchoolClass;
use App\Models\Subject;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Manage Class Subjects')]
class ManageSubjects extends Component
{
    public SchoolClass $class;
    public array $selectedSubjects = [];

    public function mount(SchoolClass $class)
    {
        $this->class = $class;
        $this->selectedSubjects = $class->defaultSubjects->pluck('id')->toArray();
    }

    public function save()
    {
        $this->class->defaultSubjects()->sync($this->selectedSubjects);
        
        $this->dispatch('alert', message: 'Class subjects updated successfully.', type: 'success');
    }

    public function render()
    {
        return view('livewire.classes.manage-subjects', [
            'allSubjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }
}
