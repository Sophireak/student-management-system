<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $search  = $request->input('search');
        $gradeId = $request->input('grade_id');

        $subjects = Subject::with('grade')
            ->when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
            )
            ->when($gradeId, fn($q) => $q->where('grade_id', $gradeId))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $grades = Grade::orderBy('level')->get();

        return view('admin.subjects.index', compact('subjects', 'grades', 'search', 'gradeId'));
    }

    public function create(): View
    {
        $grades = Grade::orderBy('level')->get();
        return view('admin.subjects.create', compact('grades'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        $grades = Grade::orderBy('level')->get();
        return view('admin.subjects.edit', compact('subject', 'grades'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    public function show(Subject $subject): RedirectResponse
    {
        return redirect()->route('admin.subjects.index');
    }
}