<?php

namespace App\Http\Controllers\Admin;

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

    public function index(Request $request): View
    {
        $classId     = $request->integer('class_id') ?: null;
        $filterData  = $this->service->getFilterData($classId);

        return view('admin.score-sheet.index', array_merge(
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

        $sheet      = $this->service->buildSheet($examSession);
        $filterData = $this->service->getFilterData($examSession->class_id);

        return view('admin.score-sheet.sheet', array_merge(
            $sheet,
            $filterData,
            ['selectedClassId' => $examSession->class_id]
        ));
    }

    public function save(SaveScoreSheetRequest $request): RedirectResponse
    {
        $result = $this->service->saveSheet(
            $request->exam_session_id,
            $request->scores,
            auth()->id()
        );

        return redirect()
            ->route('admin.score-sheet.load', [
                'exam_session_id' => $request->exam_session_id,
            ])
            ->with(
                'success',
                "{$result['saved']} score(s) saved." .
                    ($result['skipped'] > 0
                        ? " {$result['skipped']} blank cell(s) skipped."
                        : '')
            );
    }
}
