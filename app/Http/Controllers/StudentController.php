<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SubjectAllocation;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function show(Student $student): View
    {
        $user = auth()->user();
        if ($user?->role === 'teacher') {
            $allowed = SubjectAllocation::query()
                ->where('teacher_id', $user->id)
                ->where('class_id', $student->class_id)
                ->exists();

            abort_unless($allowed, 403);
        }

        $student->load(['schoolClass', 'section']);

        return view('pages.students.show', [
            'student' => $student,
        ]);
    }
}
