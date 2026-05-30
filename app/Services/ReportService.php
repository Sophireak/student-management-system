<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Collection;

class ReportService
{
    // ----------------------------------------------------------------
    // STUDENT REPORT CARD
    // Single student, one enrollment (class + year)
    // Returns scores grouped by subject, attendance summary per subject
    // ----------------------------------------------------------------
    public function studentReport(Student $student, Enrollment $enrollment): array
    {
        $enrollment->loadMissing([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'scores.examSession.subject',
            'attendances.attendanceSession.subject',
        ]);

        // Group scores by subject name
        $scoresBySubject = $enrollment->scores
            ->groupBy(fn($score) =>
                $score->examSession->subject->name
            )
            ->map(fn($scores, $subjectName) => [
                'subject' => $subjectName,
                'scores'  => $scores,
                'total'   => $scores->sum('score'),
                'max'     => $scores->sum(fn($s) => $s->examSession->max_score),
                'average' => round($scores->avg('score'), 1),
                'count'   => $scores->count(),
            ]);

        // Attendance summary per subject
        $attendanceBySubject = $enrollment->attendances
            ->groupBy(fn($a) =>
                $a->attendanceSession->subject->name
            )
            ->map(fn($records, $subjectName) => [
                'subject' => $subjectName,
                'total'   => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'absent'  => $records->where('status', 'absent')->count(),
                'late'    => $records->where('status', 'late')->count(),
                'excused' => $records->where('status', 'excused')->count(),
                'rate'    => $records->count() > 0
                    ? round(
                        ($records->whereIn('status', ['present', 'late'])->count()
                            / $records->count()) * 100,
                        1
                    )
                    : 0,
            ]);

        // Overall attendance
        $allAttendances     = $enrollment->attendances;
        $totalSessions      = $allAttendances->count();
        $overallPresent     = $allAttendances->where('status', 'present')->count();
        $overallLate        = $allAttendances->where('status', 'late')->count();
        $overallAbsent      = $allAttendances->where('status', 'absent')->count();
        $overallExcused     = $allAttendances->where('status', 'excused')->count();
        $overallRate        = $totalSessions > 0
            ? round((($overallPresent + $overallLate) / $totalSessions) * 100, 1)
            : 0;

        return [
            'student'              => $student,
            'enrollment'           => $enrollment,
            'scoresBySubject'      => $scoresBySubject,
            'attendanceBySubject'  => $attendanceBySubject,
            'totalSessions'        => $totalSessions,
            'overallPresent'       => $overallPresent,
            'overallLate'          => $overallLate,
            'overallAbsent'        => $overallAbsent,
            'overallExcused'       => $overallExcused,
            'overallRate'          => $overallRate,
        ];
    }

    // ----------------------------------------------------------------
    // CLASS PERFORMANCE REPORT
    // All students in a class, all exam sessions
    // Returns a matrix + per-session stats
    // ----------------------------------------------------------------
    public function classReport(SchoolClass $class): array
    {
        $class->loadMissing([
            'grade',
            'academicYear',
            'examSessions.subject',
            'enrollments.student',
            'enrollments.scores.examSession',
        ]);

        // Score matrix: enrollment_id → exam_session_id → score
        $matrix = [];

        foreach ($class->enrollments as $enrollment) {
            foreach ($enrollment->scores as $score) {
                $matrix[$enrollment->id][$score->exam_session_id] = $score;
            }
        }

        // Per-session stats: avg, high, low, submission count
        $sessionStats = [];

        foreach ($class->examSessions as $session) {
            $sessionScores = collect($matrix)
                ->map(fn($row) =>
                    isset($row[$session->id])
                        ? (float) $row[$session->id]->score
                        : null
                )
                ->filter()
                ->values();

            $sessionStats[$session->id] = [
                'count'   => $sessionScores->count(),
                'average' => $sessionScores->isNotEmpty()
                    ? round($sessionScores->avg(), 1) : null,
                'highest' => $sessionScores->isNotEmpty()
                    ? $sessionScores->max() : null,
                'lowest'  => $sessionScores->isNotEmpty()
                    ? $sessionScores->min() : null,
            ];
        }

        // Group sessions by subject for column grouping in view
        $sessionsBySubject = $class->examSessions
            ->groupBy(fn($s) => $s->subject->name);

        return [
            'class'             => $class,
            'matrix'            => $matrix,
            'sessionStats'      => $sessionStats,
            'sessionsBySubject' => $sessionsBySubject,
        ];
    }

    // ----------------------------------------------------------------
    // CLASS ATTENDANCE REPORT
    // All students in a class, all attendance sessions
    // Returns attendance matrix + per-student summary
    // ----------------------------------------------------------------
    public function classAttendanceReport(SchoolClass $class): array
    {
        $class->loadMissing([
            'grade',
            'academicYear',
            'attendanceSessions.subject',
            'enrollments.student',
            'enrollments.attendances.attendanceSession',
        ]);

        // Attendance matrix: enrollment_id → session_id → attendance
        $matrix = [];

        foreach ($class->enrollments as $enrollment) {
            foreach ($enrollment->attendances as $attendance) {
                $matrix[$enrollment->id][$attendance->attendance_session_id]
                    = $attendance;
            }
        }

        // Per-student summary: totals + rate
        $studentSummary = [];

        foreach ($class->enrollments as $enrollment) {
            $records = $enrollment->attendances;
            $total   = $records->count();

            $studentSummary[$enrollment->id] = [
                'total'   => $total,
                'present' => $records->where('status', 'present')->count(),
                'absent'  => $records->where('status', 'absent')->count(),
                'late'    => $records->where('status', 'late')->count(),
                'excused' => $records->where('status', 'excused')->count(),
                'rate'    => $total > 0
                    ? round(
                        ($records->whereIn('status', ['present', 'late'])->count()
                            / $total) * 100,
                        1
                    )
                    : 0,
            ];
        }

        // Group sessions by subject for column grouping
        $sessionsBySubject = $class->attendanceSessions
            ->groupBy(fn($s) => $s->subject->name);

        return [
            'class'             => $class,
            'matrix'            => $matrix,
            'studentSummary'    => $studentSummary,
            'sessionsBySubject' => $sessionsBySubject,
        ];
    }

    // ----------------------------------------------------------------
    // SYSTEM-WIDE ATTENDANCE OVERVIEW
    // Used on admin attendance report index
    // Returns per-class summary for the active year
    // ----------------------------------------------------------------
    public function attendanceOverview(): Collection
    {
        return SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) =>
                $q->where('is_active', true)
            )
            ->withCount([
                'enrollments as total_students' => fn($q) =>
                    $q->where('status', 'active'),
                'attendanceSessions as total_sessions',
            ])
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();
    }
}