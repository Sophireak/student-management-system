<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamSessionRequest;
use App\Models\ExamSession;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamSessionController extends Controller
{
    public function index(): View
    {
        $examSessions = ExamSession::with([
                            'schoolClass.grade',
                            'schoolClass.academicYear',
                            'subject',
                        ])
                        ->withCount('scores')
                        ->latest('exam_date')
                        ->paginate(20);

        return view('admin.exam-sessions.index', compact('examSessions'));
    }

    public function create(): View
    {
        $classes  = SchoolClass::with(['grade', 'academicYear'])
                               ->whereHas('academicYear', fn($q) =>
                                   $q->where('is_active', true)
                               )
                               ->orderBy('grade_id')
                               ->orderBy('name')
                               ->get();

        $subjects = Subject::with('grade')
                           ->orderBy('grade_id')
                           ->get();

        return view('admin.exam-sessions.create', compact('classes', 'subjects'));
    }

    public function store(StoreExamSessionRequest $request): RedirectResponse
    {
        // Verify class has active enrollments
        $activeCount = \App\Models\Enrollment::where('class_id', $request->class_id)
                                             ->where('status', 'active')
                                             ->count();

        if ($activeCount === 0) {
            return back()->withInput()
                ->with('error', 'Cannot create an exam for a class with no active enrollments.');
        }

        $session = ExamSession::create($request->validated());

        return redirect()
            ->route('admin.exam-sessions.show', $session)
            ->with('success', 'Exam session created.');
    }

    public function show(ExamSession $examSession): View
    {
        $examSession->load([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
            'scores.enrollment.student',
        ]);

        return view('admin.exam-sessions.show', compact('examSession'));
    }

    public function destroy(ExamSession $examSession): RedirectResponse
    {
        if ($examSession->scores()->exists()) {
            return redirect()
                ->route('admin.exam-sessions.index')
                ->with('error', 'Cannot delete an exam session that has scores recorded.');
        }

        $examSession->delete();

        return redirect()
            ->route('admin.exam-sessions.index')
            ->with('success', 'Exam session deleted.');
    }
}