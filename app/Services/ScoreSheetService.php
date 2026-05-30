<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\ExamSession;
use App\Models\SchoolClass;
use App\Models\Score;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScoreSheetService
{
    // ----------------------------------------------------------------
    // BUILD THE SHEET DATA
    // Returns everything the view needs in one structured array.
    // Single query per concern — no loops that query.
    // ----------------------------------------------------------------
    public function buildSheet(ExamSession $examSession): array
    {
        $examSession->loadMissing([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
        ]);

        $class = $examSession->schoolClass;

        // 1. Load all active enrollments for this class
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        // 2. Load all subjects for this grade
        $subjects = Subject::where('grade_id', $class->grade_id)
            ->orderBy('name')
            ->get();

        // 3. Load all existing scores for this exam session in one query
        //    Key them as enrollment_id:subject_id for O(1) lookup
        $existingScores = Score::where('exam_session_id', $examSession->id)
            ->get()
            ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");

        // 4. Build lookup matrix in PHP — no further queries
        //    $matrix[enrollment_id][subject_id] = score value or null
        $matrix = [];

        foreach ($enrollments as $enrollment) {
            foreach ($subjects as $subject) {
                $key = "{$enrollment->id}:{$subject->id}";
                $matrix[$enrollment->id][$subject->id] =
                    $existingScores->get($key)?->score;
            }
        }

        return [
            'examSession' => $examSession,
            'class'       => $class,
            'enrollments' => $enrollments,
            'subjects'    => $subjects,
            'matrix'      => $matrix,
            'maxScore'    => $examSession->max_score,
        ];
    }

    // ----------------------------------------------------------------
    // BULK SAVE — UPSERT STRATEGY
    // One upsert call per subject column.
    // Skips blank scores — does not delete existing.
    // ----------------------------------------------------------------
    public function saveSheet(
        int $examSessionId,
        array $scores,
        int $enteredBy
    ): array {
        $examSession = ExamSession::findOrFail($examSessionId);
        $saved       = 0;
        $skipped     = 0;

        // Group incoming scores by subject for batch processing
        $grouped = collect($scores)->groupBy('subject_id');

        DB::transaction(function () use (
            $grouped,
            $examSession,
            $enteredBy,
            &$saved,
            &$skipped
        ) {
            foreach ($grouped as $subjectId => $rows) {
                $upsertData = [];

                foreach ($rows as $row) {
                    // Skip empty cells — teacher left them blank
                    if ($row['score'] === null || $row['score'] === '') {
                        $skipped++;
                        continue;
                    }

                    // Verify enrollment belongs to this session's class
                    $validEnrollment = Enrollment::where('id', $row['enrollment_id'])
                        ->where('class_id', $examSession->class_id)
                        ->where('status', 'active')
                        ->exists();

                    if (! $validEnrollment) {
                        $skipped++;
                        continue;
                    }

                    // Clamp to max score
                    $value = min(
                        (float) $row['score'],
                        (float) $examSession->max_score
                    );

                    $upsertData[] = [
                        'enrollment_id'   => $row['enrollment_id'],
                        'exam_session_id' => $examSession->id,
                        'subject_id'      => $subjectId,
                        'score'           => $value,
                        'entered_by'      => $enteredBy,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];

                    $saved++;
                }

                if (! empty($upsertData)) {
                    // Single upsert per subject — replaces individual
                    // insert/update loops entirely
                    DB::table('scores')->upsert(
                        $upsertData,
                        ['enrollment_id', 'exam_session_id', 'subject_id'],
                        ['score', 'entered_by', 'updated_at']
                    );
                }
            }
        });

        return ['saved' => $saved, 'skipped' => $skipped];
    }

    // ----------------------------------------------------------------
    // FILTER DATA
    // Loads dropdowns for the filter form.
    // ----------------------------------------------------------------
    public function getFilterData(?int $classId = null): array
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $examSessions = $classId
            ? ExamSession::with('subject')
            ->where('class_id', $classId)
            ->orderBy('exam_date')
            ->get()
            : collect();

        return compact('academicYears', 'classes', 'examSessions');
    }

    // ----------------------------------------------------------------
    // TEACHER FILTER DATA
    // Scoped to teacher's assigned classes only.
    // ----------------------------------------------------------------
    public function getTeacherFilterData(int $teacherId, ?int $classId = null): array
    {
        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas(
                'classTeachers',
                fn($q) =>
                $q->where('teacher_id', $teacherId)
            )
            ->whereHas(
                'academicYear',
                fn($q) =>
                $q->where('is_active', true)
            )
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $examSessions = $classId
            ? ExamSession::with('subject')
            ->where('class_id', $classId)
            ->orderBy('exam_date')
            ->get()
            : collect();

        return compact('classes', 'examSessions');
    }
}
