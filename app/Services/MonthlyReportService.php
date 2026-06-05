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
    public function buildSheet(
        SchoolClass $class,
        int $month,
        int $academicYearId
    ): array {
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $subjects = Subject::where('grade_id', $class->grade_id)
                           ->orderBy('name')
                           ->get();

        $enrollmentIds = $enrollments->pluck('id');

        $existing = MonthlyScore::whereIn('enrollment_id', $enrollmentIds)
            ->where('academic_year_id', $academicYearId)
            ->where('month', $month)
            ->get()
            ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");

        $matrix = [];
        foreach ($enrollments as $enrollment) {
            foreach ($subjects as $subject) {
                $key = "{$enrollment->id}:{$subject->id}";
                $matrix[$enrollment->id][$subject->id] = $existing->get($key);
            }
        }

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

    public function saveSheet(
        int $classId,
        int $academicYearId,
        int $month,
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
                    if (! isset($validEnrollmentIds[$row['enrollment_id']])) {
                        $skipped++;
                        continue;
                    }

                    // Use ?? so missing keys default to null safely
                    $score    = $row['score']     ?? null;
                    $grade    = $row['grade']     ?? null;
                    $passFail = $row['pass_fail'] ?? null;

                    // Skip completely empty rows
                    $hasValue = ($score !== null && $score !== '')
                        || ($grade !== null && $grade !== '')
                        || ($passFail !== null && $passFail !== '');

                    if (! $hasValue) {
                        $skipped++;
                        continue;
                    }

                    $upsertData[] = [
                        'enrollment_id'    => $row['enrollment_id'],
                        'subject_id'       => $subjectId,
                        'academic_year_id' => $academicYearId,
                        'month'            => $month,
                        'score'            => ($score !== null && $score !== '')
                            ? (float) $score : null,
                        'grade'            => $grade,
                        'pass_fail'        => $passFail,
                        'entered_by'       => $enteredBy,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ];

                    $saved++;
                }

                if (! empty($upsertData)) {
                    DB::table('monthly_scores')->upsert(
                        $upsertData,
                        ['enrollment_id', 'subject_id', 'month', 'academic_year_id'],
                        ['score', 'grade', 'pass_fail', 'entered_by', 'updated_at']
                    );
                }
            }
        });

        return ['saved' => $saved, 'skipped' => $skipped];
    }

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