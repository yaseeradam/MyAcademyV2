<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function show(Student $student): View
    {
        $student->load(['schoolClass', 'section']);

        return view('pages.students.show', [
            'student' => $student,
        ]);
    }
}

