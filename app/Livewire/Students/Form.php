<?php

namespace App\Livewire\Students;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Student')]
class Form extends Component
{
    use WithFileUploads;

    public ?Student $student = null;

    public string $admission_number = '';
    public string $first_name = '';
    public string $last_name = '';
    public ?int $class_id = null;
    public ?int $section_id = null;
    public string $gender = 'Male';
    public ?string $dob = null;
    public ?string $blood_group = null;
    public ?string $guardian_name = null;
    public ?string $guardian_phone = null;
    public ?string $guardian_address = null;
    public string $status = 'Active';

    public $passport = null;

    public function mount(?Student $student = null): void
    {
        abort_unless(auth()->user()?->role === 'admin', 403);

        $this->student = $student;

        if ($student) {
            $this->admission_number = $student->admission_number;
            $this->first_name = $student->first_name;
            $this->last_name = $student->last_name;
            $this->class_id = $student->class_id;
            $this->section_id = $student->section_id;
            $this->gender = $student->gender;
            $this->dob = $student->dob?->format('Y-m-d');
            $this->blood_group = $student->blood_group;
            $this->guardian_name = $student->guardian_name;
            $this->guardian_phone = $student->guardian_phone;
            $this->guardian_address = $student->guardian_address;
            $this->status = $student->status;
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
        if (! $this->class_id) {
            return collect();
        }

        return Section::query()->where('class_id', $this->class_id)->orderBy('name')->get();
    }

    public function updatedClassId(): void
    {
        $this->section_id = null;
    }

    public function save()
    {
        $id = $this->student?->id;

        $data = $this->validate([
            'admission_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'admission_number')->ignore($id),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'integer', Rule::exists('classes', 'id')],
            'section_id' => ['required', 'integer', Rule::exists('sections', 'id')],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'dob' => ['nullable', 'date'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'guardian_address' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Active', 'Graduated', 'Expelled'])],
            'passport' => ['nullable', 'image', 'max:2048'],
        ]);

        $student = $this->student ?? new Student();
        $student->fill($data);

        if ($this->passport) {
            $ext = $this->passport->getClientOriginalExtension() ?: 'jpg';
            $filename = $data['admission_number'].'-'.now()->format('YmdHis').'.'.$ext;
            $path = $this->passport->storeAs('passports', $filename, 'uploads');
            $student->passport_photo = $path;
        }

        $student->save();

        return redirect()->route('students.show', $student);
    }

    public function render()
    {
        return view('livewire.students.form');
    }
}
