<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\MonthlyReportLock;
use App\Models\MonthlyScore;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class MonthlyReportService
{
    // ----------------------------------------------------------------
    // LOAD SHEET DATA
    // 3 queries total regardless of class or subject count.
    // ----------------------------------------------------------------
    public function buildSheet(
        SchoolClass $class,
        int $month,
        int $academicYearId
    ): array {
        // Query 1 — enrolled students for this class
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        // Query 2 — subjects for this grade, ordered for display
        $subjects = Subject::where('grade_id', $class->grade_id)
                           ->orderBy('name')
                           ->get();

        // Query 3 — all existing scores for this class+month+year
        // Keyed as enrollment_id:subject_id for O(1) lookup
        $enrollmentIds = $enrollments->pluck('id');

        $existing = MonthlyScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('month', $month)
            ->get()
            ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");

        // Build matrix in PHP — zero additional queries
        $matrix = [];
        foreach ($enrollments as $enrollment) {
            foreach ($subjects as $subject) {
                $key = "{$enrollment->id}:{$subject->id}";
                $matrix[$enrollment->id][$subject->id] =
                    $existing->get($key);
            }
        }

        // Check lock status — Query 4 (single row lookup)
        $isLocked = MonthlyReportLock::where('class_id', $class->id)
            ->where('academic_year_id', $academicYearId)
            ->where('month', $month)
            ->exists();

        return [
            'class'          => $class,
            'enrollments'    => $enrollments,
            'subjects'       => $subjects,
            'matrix'         => $matrix,
            'month'          => $month,
            'monthName'      => MonthlyScore::monthName($month),
            'academicYearId' => $academicYearId,
            'isLocked'       => $isLocked,
        ];
    }

    // ----------------------------------------------------------------
    // BULK SAVE — UPSERT PER SUBJECT GROUP
    // Groups incoming data by subject.
    // One upsert call per subject = minimal DB round trips.
    // ----------------------------------------------------------------
    public function saveSheet(
        int $classId,
        int $academicYearId,
        int $month,
        array $scores,
        int $enteredBy
    ): array {
        $saved   = 0;
        $skipped = 0;

        // Verify class has active enrollments
        $validEnrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id')
            ->flip(); // flip for O(1) isset() check

        // Group by subject for batch upsert
        $grouped = collect($scores)->groupBy('subject_id');

        DB::transaction(function () use (
            $grouped,
            $academicYearId,
            $month,
            $enteredBy,
            $validEnrollmentIds,
            &$saved,
            &$skipped
        ) {
            foreach ($grouped as $subjectId => $rows) {
                $upsertData = [];

                foreach ($rows as $row) {
                    // Skip rows not belonging to this class
                    if (! isset($validEnrollmentIds[$row['enrollment_id']])) {
                        $skipped++;
                        continue;
                    }

                    // Skip completely empty rows
                    $hasValue = ($row['score'] !== null && $row['score'] !== '')
                        || ($row['grade'] !== null && $row['grade'] !== '')
                        || ($row['pass_fail'] !== null && $row['pass_fail'] !== '');

                    if (! $hasValue) {
                        $skipped++;
                        continue;
                    }

                    $upsertData[] = [
                        'enrollment_id'   => $row['enrollment_id'],
                        'subject_id'      => $subjectId,
                        'academic_year_id'=> $academicYearId,
                        'month'           => $month,
                        'score'           => $row['score'] !== ''
                            ? (float) $row['score'] : null,
                        'grade'           => $row['grade'] ?? null,
                        'pass_fail'       => $row['pass_fail'] ?? null,
                        'entered_by'      => $enteredBy,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];

                    $saved++;
                }

                if (! empty($upsertData)) {
                    DB::table('monthly_scores')->upsert(
                        $upsertData,
                        // Unique key columns
                        ['enrollment_id', 'subject_id', 'month', 'academic_year_id'],
                        // Columns to update on conflict
                        ['score', 'grade', 'pass_fail', 'entered_by', 'updated_at']
                    );
                }
            }
        });

        return ['saved' => $saved, 'skipped' => $skipped];
    }

    // ----------------------------------------------------------------
    // LOCK / UNLOCK — Admin only
    // ----------------------------------------------------------------
    public function lockReport(
        int $classId,
        int $academicYearId,
        int $month,
        int $lockedBy
    ): void {
        MonthlyReportLock::updateOrCreate(
            [
                'class_id'         => $classId,
                'academic_year_id' => $academicYearId,
                'month'            => $month,
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
        int $month
    ): void {
        MonthlyReportLock::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->where('month', $month)
            ->delete();
    }

    // ----------------------------------------------------------------
    // FILTER DATA
    // ----------------------------------------------------------------
    public function getFilterData(): array
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $classes = SchoolClass::with(['grade', 'academicYear'])
                              ->orderBy('academic_year_id', 'desc')
                              ->orderBy('grade_id')
                              ->orderBy('name')
                              ->get();

        $months = collect(range(1, 9))
            ->mapWithKeys(fn($m) => [
                $m => MonthlyScore::monthName($m)
            ]);

        return compact('academicYears', 'classes', 'months');
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

        $months = collect(range(1, 9))
            ->mapWithKeys(fn($m) => [
                $m => MonthlyScore::monthName($m)
            ]);

        return compact('classes', 'months');
    }
}