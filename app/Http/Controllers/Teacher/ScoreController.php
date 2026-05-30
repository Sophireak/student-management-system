<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScoreRequest;
use App\Http\Requests\UpdateScoreRequest;
use App\Models\Enrollment;
use App\Models\ExamSession;
use App\Models\Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScoreController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    private function authorizeSession(ExamSession $session): void
    {
        $isAssigned = $this->getTeacher()
                           ->classes()
                           ->where('classes.id', $session->class_id)
                           ->exists();

        if (! $isAssigned) abort(403);
    }

    public function index(ExamSession $examSession): View
    {
        $this->authorizeSession($examSession);

        $examSession->load([
            'schoolClass.grade',
            'subject',
            'scores.enrollment.student',
        ]);

        return view('teacher.exam-sessions.scores', compact('examSession'));
    }

    public function store(
        StoreScoreRequest $request,
        ExamSession $examSession
    ): RedirectResponse {
        $this->authorizeSession($examSession);

        $records  = $request->validated()['scores'];
        $inserted = 0;
        $skipped  = 0;

        foreach ($records as $record) {
            // Skip if score already exists for this enrollment + session
            $exists = Score::where('enrollment_id',   $record['enrollment_id'])
                           ->where('exam_session_id', $examSession->id)
                           ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Verify enrollment belongs to this session's class
            $validEnrollment = Enrollment::where('id',       $record['enrollment_id'])
                                         ->where('class_id', $examSession->class_id)
                                         ->where('status',   'active')
                                         ->exists();

            if (! $validEnrollment) continue;

            // Enforce max score
            $score = min((float) $record['score'], (float) $examSession->max_score);

            Score::create([
                'enrollment_id'   => $record['enrollment_id'],
                'exam_session_id' => $examSession->id,
                'score'           => $score,
                'remarks'         => $record['remarks'] ?? null,
            ]);

            $inserted++;
        }

        $message = "{$inserted} score(s) saved.";
        if ($skipped > 0) {
            $message .= " {$skipped} already entered score(s) skipped.";
        }

        return redirect()
            ->route('teacher.exam-sessions.show', $examSession)
            ->with('success', $message);
    }

    public function update(
        UpdateScoreRequest $request,
        ExamSession $examSession,
        Score $score
    ): RedirectResponse {
        $this->authorizeSession($examSession);

        if ($score->exam_session_id !== $examSession->id) abort(403);

        // Enforce max score on update too
        $value = min((float) $request->score, (float) $examSession->max_score);

        $score->update([
            'score'   => $value,
            'remarks' => $request->remarks,
        ]);

        return redirect()
            ->route('teacher.exam-sessions.show', $examSession)
            ->with('success', 'Score updated.');
    }
}