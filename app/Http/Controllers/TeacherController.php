<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectAllocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function create()
    {
        return view('pages.teachers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $safeName = Str::of($data['name'])->lower()->replaceMatches('/[^a-z0-9]+/i', '-')->trim('-')->toString();
            $filename = ($safeName ?: 'teacher').'-'.now()->format('YmdHis').'.'.$ext;
            $profilePhotoPath = $file->storeAs('teacher-photos', $filename, 'uploads');
        }

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'teacher',
            'is_active' => (bool) ($data['is_active'] ?? false),
            'profile_photo' => $profilePhotoPath,
        ]);

        return redirect()
            ->route('teachers')
            ->with('status', 'Teacher added successfully.');
    }

    public function show(User $teacher)
    {
        abort_unless($teacher->role === 'teacher', 404);

        $allocations = SubjectAllocation::query()
            ->with(['subject', 'schoolClass'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('class_id')
            ->orderBy('subject_id')
            ->get();

        $classes = SchoolClass::query()->orderBy('level')->get();
        $subjects = Subject::query()->orderBy('name')->get();

        return view('pages.teachers.show', compact('teacher', 'allocations', 'classes', 'subjects'));
    }

    public function updatePhoto(Request $request, User $teacher)
    {
        abort_unless($teacher->role === 'teacher', 404);

        $data = $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $file = $data['photo'];
        $ext = $file->getClientOriginalExtension() ?: 'jpg';

        $safeName = Str::of($teacher->name)->lower()->replaceMatches('/[^a-z0-9]+/i', '-')->trim('-')->toString();
        $filename = ($safeName ?: 'teacher').'-'.now()->format('YmdHis').'.'.$ext;
        $path = $file->storeAs('teacher-photos', $filename, 'uploads');

        if ($teacher->profile_photo) {
            Storage::disk('uploads')->delete($teacher->profile_photo);
        }

        $teacher->profile_photo = $path;
        $teacher->save();

        return back()->with('status', 'Profile photo updated.');
    }

    public function storeAllocation(Request $request, User $teacher)
    {
        abort_unless($teacher->role === 'teacher', 404);

        $data = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
        ]);

        try {
            SubjectAllocation::query()->create([
                'teacher_id' => $teacher->id,
                'class_id' => $data['class_id'],
                'subject_id' => $data['subject_id'],
            ]);
        } catch (QueryException $e) {
            // Ignore duplicate allocation attempts (unique constraint).
        }

        return back()->with('status', 'Allocation saved.');
    }

    public function destroyAllocation(User $teacher, SubjectAllocation $allocation)
    {
        abort_unless($teacher->role === 'teacher', 404);
        abort_unless((int) $allocation->teacher_id === (int) $teacher->id, 404);

        $allocation->delete();

        return back()->with('status', 'Allocation removed.');
    }
}
