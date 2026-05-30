<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveScoreSheetRequest;
use App\Models\ExamSession;
use App\Services\ScoreSheetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreSheetController extends Controller
{
    public function __construct(private ScoreSheetService $service) {}

    private function getTeacher()
    {
        $teacher = auth()->user()->teacher;
        if (! $teacher) abort(403, 'Teacher profile not found.');
        return $teacher;
    }

    private function authorizeClass(int $classId): void
    {
        $assigned = $this->getTeacher()
                         ->classes()
                         ->where('classes.id', $classId)
                         ->exists();

        if (! $assigned) abort(403, 'You are not assigned to this class.');
    }

    public function index(Request $request): View
    {
        $teacher     = $this->getTeacher();
        $classId     = $request->integer('class_id') ?: null;
        $filterData  = $this->service->getTeacherFilterData(
            $teacher->id,
            $classId
        );

        return view('teacher.score-sheet.index', array_merge(
            $filterData,
            ['selectedClassId' => $classId]
        ));
    }

    public function load(Request $request): View
    {
        $request->validate([
            'exam_session_id' => ['required', 'exists:exam_sessions,id'],
        ]);

        $examSession = ExamSession::with([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
        ])->findOrFail($request->exam_session_id);

        // Authorize before building sheet
        $this->authorizeClass($examSession->class_id);

        $teacher    = $this->getTeacher();
        $sheet      = $this->service->buildSheet($examSession);
        $filterData = $this->service->getTeacherFilterData(
            $teacher->id,
            $examSession->class_id
        );

        return view('teacher.score-sheet.sheet', array_merge(
            $sheet,
            $filterData,
            ['selectedClassId' => $examSession->class_id]
        ));
    }

    public function save(SaveScoreSheetRequest $request): RedirectResponse
    {
        $examSession = ExamSession::findOrFail($request->exam_session_id);

        // Authorize before saving
        $this->authorizeClass($examSession->class_id);

        $result = $this->service->saveSheet(
            $request->exam_session_id,
            $request->scores,
            auth()->id()
        );

        return redirect()
            ->route('teacher.score-sheet.load', [
                'exam_session_id' => $request->exam_session_id,
            ])
            ->with('success',
                "{$result['saved']} score(s) saved." .
                ($result['skipped'] > 0
                    ? " {$result['skipped']} blank cell(s) skipped."
                    : '')
            );
    }
}