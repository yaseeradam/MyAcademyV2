<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SubjectAllocation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
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

    public function destroy(Student $student)
    {
        $photo = $student->passport_photo ? str_replace('\\', '/', (string) $student->passport_photo) : null;

        try {
            $student->delete();
        } catch (QueryException $e) {
            return back()->withErrors(['student' => 'Unable to delete this student. Remove dependent records first.']);
        }

        if ($photo) {
            Storage::disk('uploads')->delete($photo);
        }

        return redirect()
            ->route('students.index')
            ->with('status', 'Student deleted.');
    }
}
