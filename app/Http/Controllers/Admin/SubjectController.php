<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with('grade')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
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
        if ($subject->examSessions()->exists()) {
            return redirect()
                ->route('admin.subjects.index')
                ->with('error', 'Cannot delete this subject because it has exam sessions.');
        }

        if ($subject->attendanceSessions()->exists()) {
            return redirect()
                ->route('admin.subjects.index')
                ->with('error', 'Cannot delete this subject because it has attendance sessions.');
        }

        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    // No show() needed
    public function show(Subject $subject): RedirectResponse
    {
        return redirect()->route('admin.subjects.index');
    }
}
