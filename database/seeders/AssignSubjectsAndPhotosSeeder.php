<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssignSubjectsAndPhotosSeeder extends Seeder
{
    public function run(): void
    {
        // Assign all subjects to all classes
        $classes = SchoolClass::all();
        $subjects = Subject::all();

        foreach ($classes as $class) {
            foreach ($subjects as $subject) {
                DB::table('class_subject')->insertOrIgnore([
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'is_core' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info("Assigned {$subjects->count()} subjects to {$classes->count()} classes!");

        // Generate avatar URLs for students
        $students = Student::all();
        foreach ($students as $student) {
            $gender = strtolower($student->gender);
            $seed = urlencode($student->first_name . ' ' . $student->last_name);
            $student->passport_photo = "https://api.dicebear.com/7.x/avataaars/svg?seed={$seed}&gender={$gender}";
            $student->save();
        }

        $this->command->info("Added photos to {$students->count()} students!");

        // Generate avatar URLs for teachers
        $teachers = User::where('role', 'teacher')->get();
        foreach ($teachers as $teacher) {
            $seed = urlencode($teacher->name);
            $teacher->profile_photo = "https://api.dicebear.com/7.x/avataaars/svg?seed={$seed}";
            $teacher->save();
        }

        $this->command->info("Added photos to {$teachers->count()} teachers!");
    }
}
