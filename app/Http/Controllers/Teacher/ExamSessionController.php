<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExamSessionRequest;
use App\Models\Enrollment;
use App\Models\ExamSession;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExamSessionController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(): View
    {
        $teacher  = $this->getTeacher();
        $classIds = $teacher->classes()->pluck('classes.id');

        $examSessions = ExamSession::with([
                            'schoolClass.grade',
                            'subject',
                        ])
                        ->withCount('scores')
                        ->whereIn('class_id', $classIds)
                        ->latest('exam_date')
                        ->paginate(20);

        return view('teacher.exam-sessions.index', compact('examSessions'));
    }

    public function create(): View
    {
        $teacher  = $this->getTeacher();

        $classes  = $teacher->classes()
                            ->with(['grade', 'academicYear'])
                            ->whereHas('academicYear', fn($q) =>
                                $q->where('is_active', true)
                            )
                            ->get();

        $subjects = Subject::with('grade')
                           ->orderBy('grade_id')
                           ->get();

        return view('teacher.exam-sessions.create', compact('classes', 'subjects'));
    }

    public function store(StoreExamSessionRequest $request): RedirectResponse
    {
        $teacher = $this->getTeacher();

        $isAssigned = $teacher->classes()
                              ->where('classes.id', $request->class_id)
                              ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not assigned to this class.');
        }

        $activeCount = Enrollment::where('class_id', $request->class_id)
                                 ->where('status', 'active')
                                 ->count();

        if ($activeCount === 0) {
            return back()->withInput()
                ->with('error', 'This class has no active enrollments.');
        }

        $session = ExamSession::create($request->validated());

        return redirect()
            ->route('teacher.exam-sessions.show', $session)
            ->with('success', 'Exam session created. Now enter scores.');
    }

    public function show(ExamSession $examSession): View
    {
        $teacher = $this->getTeacher();

        $isAssigned = $teacher->classes()
                              ->where('classes.id', $examSession->class_id)
                              ->exists();

        if (! $isAssigned) abort(403);

        $examSession->load([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
            'scores.enrollment.student',
        ]);

        $scoredEnrollmentIds = $examSession->scores()->pluck('enrollment_id');

        $unscoredEnrollments = Enrollment::with('student')
            ->where('class_id', $examSession->class_id)
            ->where('status', 'active')
            ->whereNotIn('id', $scoredEnrollmentIds)
            ->orderBy('id')
            ->get();

        $isFullyScored = $unscoredEnrollments->isEmpty();

        return view('teacher.exam-sessions.show', compact(
            'examSession',
            'unscoredEnrollments',
            'isFullyScored'
        ));
    }

    public function destroy(ExamSession $examSession): RedirectResponse
    {
        $teacher = $this->getTeacher();

        $isAssigned = $teacher->classes()
                              ->where('classes.id', $examSession->class_id)
                              ->exists();

        if (! $isAssigned) abort(403);

        if ($examSession->scores()->exists()) {
            return redirect()
                ->route('teacher.exam-sessions.index')
                ->with('error', 'Cannot delete an exam session that has scores recorded.');
        }

        $examSession->delete();

        return redirect()
            ->route('teacher.exam-sessions.index')
            ->with('success', 'Exam session deleted.');
    }
}