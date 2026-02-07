<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::query()
            ->with('sections')
            ->withCount(['sections', 'students'])
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return view('pages.classes.index', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('classes', 'name')],
            'level' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        SchoolClass::query()->create([
            'name' => $data['name'],
            'level' => (int) $data['level'],
        ]);

        return back()->with('status', 'Class added.');
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('classes', 'name')->ignore($class->id)],
            'level' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $class->update([
            'name' => $data['name'],
            'level' => (int) $data['level'],
        ]);

        return back()->with('status', 'Class updated.');
    }

    public function destroy(SchoolClass $class)
    {
        try {
            $class->delete();
        } catch (QueryException $e) {
            return back()->withErrors(['class' => 'Unable to delete this class. Remove dependent records first.']);
        }

        return back()->with('status', 'Class deleted.');
    }
}
