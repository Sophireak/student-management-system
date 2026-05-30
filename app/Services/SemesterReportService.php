<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\MonthlyScore;
use App\Models\SchoolClass;
use App\Models\SemesterReportLock;
use App\Models\SemesterScore;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SemesterReportService
{
    // ----------------------------------------------------------------
    // BUILD SHEET
    // Loads everything needed to render the semester grid.
    // 4 queries total regardless of class size.
    // ----------------------------------------------------------------
    public function buildSheet(
        SchoolClass $class,
        int $semester,
        int $academicYearId
    ): array {
        $class->loadMissing(['grade', 'academicYear']);

        // Query 1 — active enrollments with student
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        // Query 2 — subjects for this grade
        $subjects = Subject::where('grade_id', $class->grade_id)
                           ->orderBy('name')
                           ->get();

        $enrollmentIds = $enrollments->pluck('id');

        // Query 3 — existing semester scores keyed for O(1) lookup
        $existing = SemesterScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->get()
            ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");

        // Query 4 — lock status
        $isLocked = SemesterReportLock::where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->exists();

        // Build matrix in PHP — no further queries
        $matrix = [];
        foreach ($enrollments as $enrollment) {
            foreach ($subjects as $subject) {
                $key = "{$enrollment->id}:{$subject->id}";
                $matrix[$enrollment->id][$subject->id] = $existing->get($key);
            }
        }

        // Build summary row per enrollment
        // total, average, rank already stored — just read from existing
        $summary = [];
        foreach ($enrollments as $enrollment) {
            // Find any semester score row for this enrollment
            // (total/average/rank are the same across all subject rows)
            $anyScore = $existing->first(fn($s) =>
                $s->enrollment_id === $enrollment->id
            );

            $summary[$enrollment->id] = [
                'total'   => $anyScore?->total_score,
                'average' => $anyScore?->average_score,
                'rank'    => $anyScore?->rank,
            ];
        }

        // Check if monthly data exists for calculation
        $months          = SemesterScore::semesterMonths($semester);
        $hasMonthlyData  = MonthlyScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('month', $months)
            ->exists();

        return [
            'class'           => $class,
            'enrollments'     => $enrollments,
            'subjects'        => $subjects,
            'matrix'          => $matrix,
            'summary'         => $summary,
            'semester'        => $semester,
            'semesterLabel'   => SemesterScore::semesterLabel($semester),
            'academicYearId'  => $academicYearId,
            'isLocked'        => $isLocked,
            'hasMonthlyData'  => $hasMonthlyData,
        ];
    }

    // ----------------------------------------------------------------
    // CALCULATE FROM MONTHLY SCORES
    // Averages monthly scores per subject per enrollment.
    // Called by admin before editing the semester sheet.
    // ----------------------------------------------------------------
    public function calculateFromMonthly(
        SchoolClass $class,
        int $semester,
        int $academicYearId,
        int $enteredBy
    ): array {
        $months      = SemesterScore::semesterMonths($semester);
        $enrollments = Enrollment::where('class_id', $class->id)
                                 ->where('status', 'active')
                                 ->pluck('id');

        $subjects = Subject::where('grade_id', $class->grade_id)->get();

        // Load all monthly scores for this semester in one query
        $monthlyScores = MonthlyScore::whereIn('enrollment_id', $enrollments)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('month', $months)
            ->get();

        // Group: enrollment_id → subject_id → collection of monthly scores
        $grouped = $monthlyScores->groupBy(fn($s) =>
            "{$s->enrollment_id}:{$s->subject_id}"
        );

        $upsertData = [];
        $processed  = 0;

        DB::transaction(function () use (
            $enrollments, $subjects, $grouped, $months,
            $academicYearId, $semester, $enteredBy,
            &$upsertData, &$processed
        ) {
            foreach ($enrollments as $enrollmentId) {
                $numericTotal = 0;
                $numericCount = 0;

                foreach ($subjects as $subject) {
                    $key     = "{$enrollmentId}:{$subject->id}";
                    $records = $grouped->get($key, collect());

                    if ($subject->isNumeric()) {
                        // Average of numeric monthly scores
                        $scores     = $records->whereNotNull('score')
                                              ->pluck('score');
                        $avgScore   = $scores->isNotEmpty()
                            ? round($scores->avg(), 2)
                            : null;

                        if ($avgScore !== null) {
                            $numericTotal += $avgScore;
                            $numericCount++;
                        }

                        $upsertData[] = [
                            'enrollment_id'      => $enrollmentId,
                            'subject_id'         => $subject->id,
                            'academic_year_id'   => $academicYearId,
                            'semester'           => $semester,
                            'score'              => $avgScore,
                            'grade'              => null,
                            'pass_fail'          => null,
                            'total_score'        => null, // updated after all subjects
                            'average_score'      => null,
                            'rank'               => null,
                            'is_manual_override' => false,
                            'entered_by'         => $enteredBy,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ];

                    } elseif ($subject->isGrade()) {
                        // Most common grade value from monthly records
                        $mostCommon = $records->whereNotNull('grade')
                            ->groupBy('grade')
                            ->sortByDesc(fn($g) => $g->count())
                            ->keys()
                            ->first();

                        $upsertData[] = [
                            'enrollment_id'      => $enrollmentId,
                            'subject_id'         => $subject->id,
                            'academic_year_id'   => $academicYearId,
                            'semester'           => $semester,
                            'score'              => null,
                            'grade'              => $mostCommon,
                            'pass_fail'          => null,
                            'total_score'        => null,
                            'average_score'      => null,
                            'rank'               => null,
                            'is_manual_override' => false,
                            'entered_by'         => $enteredBy,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ];

                    } else {
                        // Pass/Fail: Pass if majority of months = Pass
                        $passFail = $records->whereNotNull('pass_fail')
                            ->groupBy('pass_fail')
                            ->sortByDesc(fn($g) => $g->count())
                            ->keys()
                            ->first();

                        $upsertData[] = [
                            'enrollment_id'      => $enrollmentId,
                            'subject_id'         => $subject->id,
                            'academic_year_id'   => $academicYearId,
                            'semester'           => $semester,
                            'score'              => null,
                            'grade'              => null,
                            'pass_fail'          => $passFail,
                            'total_score'        => null,
                            'average_score'      => null,
                            'rank'               => null,
                            'is_manual_override' => false,
                            'entered_by'         => $enteredBy,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ];
                    }

                    $processed++;
                }

                // Inject total and average into all rows for this enrollment
                $totalScore   = $numericCount > 0 ? round($numericTotal, 2) : null;
                $averageScore = $numericCount > 0
                    ? round($numericTotal / $numericCount, 2)
                    : null;

                foreach ($upsertData as &$row) {
                    if ($row['enrollment_id'] === $enrollmentId) {
                        $row['total_score']   = $totalScore;
                        $row['average_score'] = $averageScore;
                    }
                }
                unset($row);
            }

            // Upsert all rows
            if (! empty($upsertData)) {
                DB::table('semester_scores')->upsert(
                    $upsertData,
                    ['enrollment_id', 'subject_id', 'semester', 'academic_year_id'],
                    ['score', 'grade', 'pass_fail', 'total_score',
                     'average_score', 'is_manual_override', 'entered_by', 'updated_at']
                );
            }

            // Calculate and store ranks
            $this->recalculateRanks(
                $class->id, $academicYearId, $semester
            );
        });

        return ['processed' => $processed];
    }

    // ----------------------------------------------------------------
    // SAVE MANUAL OVERRIDES
    // Admin edits individual cells after calculation.
    // Recalculates total, average, and rank after save.
    // ----------------------------------------------------------------
    public function saveSheet(
        int $classId,
        int $academicYearId,
        int $semester,
        array $scores,
        int $enteredBy
    ): array {
        $saved   = 0;
        $skipped = 0;

        $validEnrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id')
            ->flip();

        $grouped = collect($scores)->groupBy('subject_id');

        DB::transaction(function () use (
            $grouped, $academicYearId, $semester, $enteredBy,
            $validEnrollmentIds, $classId, &$saved, &$skipped
        ) {
            foreach ($grouped as $subjectId => $rows) {
                $upsertData = [];

                foreach ($rows as $row) {
                    if (! isset($validEnrollmentIds[$row['enrollment_id']])) {
                        $skipped++;
                        continue;
                    }

                    $hasValue = ($row['score'] !== null && $row['score'] !== '')
                        || ($row['grade'] !== null && $row['grade'] !== '')
                        || ($row['pass_fail'] !== null && $row['pass_fail'] !== '');

                    if (! $hasValue) {
                        $skipped++;
                        continue;
                    }

                    $upsertData[] = [
                        'enrollment_id'      => $row['enrollment_id'],
                        'subject_id'         => $subjectId,
                        'academic_year_id'   => $academicYearId,
                        'semester'           => $semester,
                        'score'              => isset($row['score']) && $row['score'] !== ''
                            ? (float) $row['score'] : null,
                        'grade'              => $row['grade'] ?? null,
                        'pass_fail'          => $row['pass_fail'] ?? null,
                        'is_manual_override' => true,
                        'entered_by'         => $enteredBy,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];

                    $saved++;
                }

                if (! empty($upsertData)) {
                    DB::table('semester_scores')->upsert(
                        $upsertData,
                        ['enrollment_id', 'subject_id', 'semester', 'academic_year_id'],
                        ['score', 'grade', 'pass_fail', 'is_manual_override',
                         'entered_by', 'updated_at']
                    );
                }
            }

            // Recalculate totals, averages, and ranks after manual save
            $this->recalculateSummaries($classId, $academicYearId, $semester);
            $this->recalculateRanks($classId, $academicYearId, $semester);
        });

        return ['saved' => $saved, 'skipped' => $skipped];
    }

    // ----------------------------------------------------------------
    // RECALCULATE SUMMARIES
    // Updates total_score and average_score per enrollment.
    // Called after any save operation.
    // ----------------------------------------------------------------
    private function recalculateSummaries(
        int $classId,
        int $academicYearId,
        int $semester
    ): void {
        $enrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id');

        // Load numeric scores per enrollment
        $numericScores = SemesterScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->whereNotNull('score')
            ->select('enrollment_id', 'score')
            ->get()
            ->groupBy('enrollment_id');

        foreach ($numericScores as $enrollmentId => $scores) {
            $total   = round($scores->sum('score'), 2);
            $average = round($scores->avg('score'), 2);

            // Update all rows for this enrollment with new totals
            DB::table('semester_scores')
                ->where('enrollment_id', $enrollmentId)
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->update([
                    'total_score'   => $total,
                    'average_score' => $average,
                    'updated_at'    => now(),
                ]);
        }
    }

    // ----------------------------------------------------------------
    // RECALCULATE RANKS
    // Ranks all enrollments in the class by average score.
    // Same average = same rank (dense ranking).
    // ----------------------------------------------------------------
    private function recalculateRanks(
        int $classId,
        int $academicYearId,
        int $semester
    ): void {
        $enrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id');

        // Get distinct average per enrollment (one row per enrollment)
        $averages = DB::table('semester_scores')
            ->whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->whereNotNull('average_score')
            ->select('enrollment_id', 'average_score')
            ->distinct()
            ->orderByDesc('average_score')
            ->get();

        // Assign dense ranks
        $rank    = 1;
        $prevAvg = null;

        foreach ($averages as $index => $row) {
            if ($prevAvg !== null && $row->average_score < $prevAvg) {
                $rank = $index + 1;
            }
            $prevAvg = $row->average_score;

            DB::table('semester_scores')
                ->where('enrollment_id', $row->enrollment_id)
                ->where('academic_year_id', $academicYearId)
                ->where('semester', $semester)
                ->update(['rank' => $rank, 'updated_at' => now()]);
        }
    }

    // ----------------------------------------------------------------
    // LOCK / UNLOCK
    // ----------------------------------------------------------------
    public function lockReport(
        int $classId,
        int $academicYearId,
        int $semester,
        int $lockedBy
    ): void {
        SemesterReportLock::updateOrCreate(
            [
                'class_id'         => $classId,
                'academic_year_id' => $academicYearId,
                'semester'         => $semester,
            ],
            [
                'locked_by' => $lockedBy,
                'locked_at' => now(),
            ]
        );
    }

    public function unlockReport(
        int $classId,
        int $academicYearId,
        int $semester
    ): void {
        SemesterReportLock::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', $semester)
            ->delete();
    }

    // ----------------------------------------------------------------
    // FILTER DATA
    // ----------------------------------------------------------------
    public function getFilterData(): array
    {
        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')
                                                  ->get();

        $classes = SchoolClass::with(['grade', 'academicYear'])
                              ->orderBy('academic_year_id', 'desc')
                              ->orderBy('grade_id')
                              ->orderBy('name')
                              ->get();

        $semesters = [
            1 => 'Semester 1 (Sep – Jan)',
            2 => 'Semester 2 (Feb – May)',
        ];

        return compact('academicYears', 'classes', 'semesters');
    }

    public function getTeacherFilterData(int $teacherId): array
    {
        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('classTeachers', fn($q) =>
                $q->where('teacher_id', $teacherId)
            )
            ->whereHas('academicYear', fn($q) =>
                $q->where('is_active', true)
            )
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $semesters = [
            1 => 'Semester 1 (Sep – Jan)',
            2 => 'Semester 2 (Feb – May)',
        ];

        return compact('classes', 'semesters');
    }
}