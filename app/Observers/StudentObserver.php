<?php

namespace App\Observers;

use App\Models\Student;

class StudentObserver
{
    public function updating(Student $student): void
    {
        // If class is changing, clear subject overrides
        if ($student->isDirty('class_id') && $student->getOriginal('class_id') !== null) {
            $student->subjectOverrides()->detach();
        }
    }
}
