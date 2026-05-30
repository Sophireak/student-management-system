<?php

namespace App\Services;

use App\Models\AnnualReportLock;
use App\Models\AnnualScore;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\SemesterScore;
use Illuminate\Support\Facades\DB;

class AnnualReportService
{
    // ----------------------------------------------------------------
    // BUILD SHEET
    // Loads annual scores for the class.
    // 3 queries total.
    // ----------------------------------------------------------------
    public function buildSheet(
        SchoolClass $class,
        int $academicYearId
    ): array {
        $class->loadMissing(['grade', 'academicYear']);

        // Query 1 — active enrollments
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $enrollmentIds = $enrollments->pluck('id');

        // Query 2 — existing annual scores keyed by enrollment_id
        $existing = AnnualScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->get()
            ->keyBy('enrollment_id');

        // Query 3 — lock status
        $isLocked = AnnualReportLock::where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->exists();

        // Check semester data availability
        $hasSemester1 = SemesterScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', 1)
            ->exists();

        $hasSemester2 = SemesterScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', 2)
            ->exists();

        return [
            'class'          => $class,
            'enrollments'    => $enrollments,
            'existing'       => $existing,
            'academicYearId' => $academicYearId,
            'isLocked'       => $isLocked,
            'hasSemester1'   => $hasSemester1,
            'hasSemester2'   => $hasSemester2,
            'passThreshold'  => AnnualScore::PASS_THRESHOLD,
        ];
    }

    // ----------------------------------------------------------------
    // CALCULATE FROM SEMESTER DATA
    // Pulls semester averages and conduct grades per enrollment.
    // Called by admin when both semesters are ready.
    // ----------------------------------------------------------------
    public function calculateFromSemesters(
        SchoolClass $class,
        int $academicYearId,
        int $enteredBy
    ): array {
        $enrollments = Enrollment::where('class_id', $class->id)
            ->where('status', 'active')
            ->pluck('id');

        // Load semester averages and conduct in one query per semester
        // average_score stored per enrollment on semester_scores
        // We take distinct enrollment_id + average_score
        // (same value across all subject rows for that enrollment)
        $sem1Data = SemesterScore::whereIn('enrollment_id', $enrollments)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', 1)
            ->select('enrollment_id', 'average_score', 'grade')
            ->get()
            ->groupBy('enrollment_id');

        $sem2Data = SemesterScore::whereIn('enrollment_id', $enrollments)
            ->where('academic_year_id', $academicYearId)
            ->where('semester', 2)
            ->select('enrollment_id', 'average_score', 'grade')
            ->get()
            ->groupBy('enrollment_id');

        $upsertData = [];
        $processed  = 0;

        foreach ($enrollments as $enrollmentId) {
            // Get semester averages (first row is enough — avg is same on all rows)
            $s1Rows    = $sem1Data->get($enrollmentId, collect());
            $s2Rows    = $sem2Data->get($enrollmentId, collect());

            $sem1Avg   = $s1Rows->first()?->average_score;
            $sem2Avg   = $s2Rows->first()?->average_score;

            // Conduct = most common grade across semester moral rows
            $sem1Conduct = $this->dominantGrade($s1Rows);
            $sem2Conduct = $this->dominantGrade($s2Rows);

            // Final score = average of both semester averages
            $finalScore = null;
            if ($sem1Avg !== null && $sem2Avg !== null) {
                $finalScore = round(((float)$sem1Avg + (float)$sem2Avg) / 2, 2);
            } elseif ($sem1Avg !== null) {
                $finalScore = round((float)$sem1Avg, 2);
            } elseif ($sem2Avg !== null) {
                $finalScore = round((float)$sem2Avg, 2);
            }

            $isPassing = $finalScore !== null
                ? AnnualScore::isPassingScore($finalScore)
                : null;

            $upsertData[] = [
                'enrollment_id'    => $enrollmentId,
                'academic_year_id' => $academicYearId,
                'semester1_avg'    => $sem1Avg,
                'semester2_avg'    => $sem2Avg,
                'semester1_conduct'=> $sem1Conduct,
                'semester2_conduct'=> $sem2Conduct,
                'final_score'      => $finalScore,
                'is_passing'       => $isPassing,
                'rank'             => null,
                'notes'            => null,
                'is_manual_override' => false,
                'entered_by'       => $enteredBy,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            $processed++;
        }

        DB::transaction(function () use ($upsertData, $class, $academicYearId) {
            if (! empty($upsertData)) {
                DB::table('annual_scores')->upsert(
                    $upsertData,
                    ['enrollment_id', 'academic_year_id'],
                    [
                        'semester1_avg', 'semester2_avg',
                        'semester1_conduct', 'semester2_conduct',
                        'final_score', 'is_passing',
                        'is_manual_override', 'entered_by', 'updated_at',
                    ]
                );
            }

            $this->recalculateRanks($class->id, $academicYearId);
        });

        return ['processed' => $processed];
    }

    // ----------------------------------------------------------------
    // SAVE MANUAL OVERRIDES
    // Admin corrects individual student rows.
    // Recalculates final_score and rank after save.
    // ----------------------------------------------------------------
    public function saveSheet(
        int $classId,
        int $academicYearId,
        array $scores,
        int $enteredBy
    ): array {
        $validEnrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id')
            ->flip();

        $upsertData = [];
        $saved      = 0;
        $skipped    = 0;

        foreach ($scores as $row) {
            if (! isset($validEnrollmentIds[$row['enrollment_id']])) {
                $skipped++;
                continue;
            }

            $s1 = isset($row['semester1_avg']) && $row['semester1_avg'] !== ''
                ? (float) $row['semester1_avg'] : null;
            $s2 = isset($row['semester2_avg']) && $row['semester2_avg'] !== ''
                ? (float) $row['semester2_avg'] : null;

            // Recalculate final from whatever averages are provided
            $final = null;
            if ($s1 !== null && $s2 !== null) {
                $final = round(($s1 + $s2) / 2, 2);
            } elseif ($s1 !== null) {
                $final = round($s1, 2);
            } elseif ($s2 !== null) {
                $final = round($s2, 2);
            }

            // Admin can manually override is_passing
            $isPassing = isset($row['is_passing'])
                ? (bool) $row['is_passing']
                : ($final !== null
                    ? AnnualScore::isPassingScore($final)
                    : null);

            $upsertData[] = [
                'enrollment_id'    => $row['enrollment_id'],
                'academic_year_id' => $academicYearId,
                'semester1_avg'    => $s1,
                'semester2_avg'    => $s2,
                'semester1_conduct'=> $row['semester1_conduct'] ?? null,
                'semester2_conduct'=> $row['semester2_conduct'] ?? null,
                'final_score'      => $final,
                'is_passing'       => $isPassing,
                'notes'            => $row['notes'] ?? null,
                'is_manual_override' => true,
                'entered_by'       => $enteredBy,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            $saved++;
        }

        DB::transaction(function () use (
            $upsertData, $classId, $academicYearId, &$saved
        ) {
            if (! empty($upsertData)) {
                DB::table('annual_scores')->upsert(
                    $upsertData,
                    ['enrollment_id', 'academic_year_id'],
                    [
                        'semester1_avg', 'semester2_avg',
                        'semester1_conduct', 'semester2_conduct',
                        'final_score', 'is_passing', 'notes',
                        'is_manual_override', 'entered_by', 'updated_at',
                    ]
                );
            }

            $this->recalculateRanks($classId, $academicYearId);
        });

        return ['saved' => $saved, 'skipped' => $skipped];
    }

    // ----------------------------------------------------------------
    // RECALCULATE RANKS
    // Dense ranking by final_score DESC.
    // Excludes null final scores from ranking.
    // ----------------------------------------------------------------
    private function recalculateRanks(int $classId, int $academicYearId): void
    {
        $enrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id');

        $scores = DB::table('annual_scores')
            ->whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->whereNotNull('final_score')
            ->orderByDesc('final_score')
            ->select('enrollment_id', 'final_score')
            ->get();

        $rank    = 1;
        $prevScore = null;
        $sameCount = 0;

        foreach ($scores as $index => $row) {
            if ($prevScore !== null && (float)$row->final_score < (float)$prevScore) {
                $rank = $index + 1;
            }

            DB::table('annual_scores')
                ->where('enrollment_id', $row->enrollment_id)
                ->where('academic_year_id', $academicYearId)
                ->update(['rank' => $rank, 'updated_at' => now()]);

            $prevScore = $row->final_score;
        }
    }

    // ----------------------------------------------------------------
    // DOMINANT GRADE
    // Returns most common grade text from a collection.
    // Used for semester conduct.
    // ----------------------------------------------------------------
    private function dominantGrade($rows): ?string
    {
        if ($rows->isEmpty()) return null;

        return $rows->whereNotNull('grade')
            ->groupBy('grade')
            ->sortByDesc(fn($g) => $g->count())
            ->keys()
            ->first();
    }

    // ----------------------------------------------------------------
    // LOCK / UNLOCK
    // ----------------------------------------------------------------
    public function lockReport(
        int $classId,
        int $academicYearId,
        int $lockedBy
    ): void {
        AnnualReportLock::updateOrCreate(
            ['class_id' => $classId, 'academic_year_id' => $academicYearId],
            ['locked_by' => $lockedBy, 'locked_at' => now()]
        );
    }

    public function unlockReport(int $classId, int $academicYearId): void
    {
        AnnualReportLock::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
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

        return compact('academicYears', 'classes');
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

        return compact('classes');
    }
}