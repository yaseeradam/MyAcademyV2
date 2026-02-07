<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::query()
            ->orderBy('name')
            ->get();

        return view('pages.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects', 'code')],
        ]);

        Subject::query()->create([
            'name' => $data['name'],
            'code' => strtoupper(trim($data['code'])),
        ]);

        return back()->with('status', 'Subject added.');
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects', 'code')->ignore($subject->id)],
        ]);

        $subject->update([
            'name' => $data['name'],
            'code' => strtoupper(trim($data['code'])),
        ]);

        return back()->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        try {
            $subject->delete();
        } catch (QueryException $e) {
            return back()->withErrors(['subject' => 'Unable to delete this subject. Remove dependent records first.']);
        }

        return back()->with('status', 'Subject deleted.');
    }
}
