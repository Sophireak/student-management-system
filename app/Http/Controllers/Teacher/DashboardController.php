<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\MonthlyScore;
use App\Models\Subject;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user()->teacher;

        if (! $teacher) {
            return view('teacher.dashboard', [
                'classes'          => collect(),
                'recentSessions'   => collect(),
                'todayAttendance'  => null,
                'scoreProgress'    => null,
                'totalStudents'    => 0,
                'totalSessions'    => 0,
            ]);
        }

        // Get teacher's classes (active year only)
        $classes = $teacher->classes()
            ->with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->withCount([
                'enrollments as active_students' => fn($q) => $q->where('status', 'active'),
            ])
            ->get();

        $classIds = $classes->pluck('id');
        $totalStudents = $classes->sum('active_students');

        // Recent sessions
        $recentSessions = AttendanceSession::whereIn('class_id', $classIds)
            ->with(['schoolClass', 'subject'])
            ->latest('session_date')
            ->limit(5)
            ->get();

        $totalSessions = AttendanceSession::whereIn('class_id', $classIds)->count();

        // Today's attendance status
        $todayAttendance = null;
        if ($classes->isNotEmpty()) {
            $todaySessionExists = AttendanceSession::whereIn('class_id', $classIds)
                ->whereDate('session_date', today())
                ->exists();

            $todayAttendance = [
                'taken' => $todaySessionExists,
                'date'  => today()->format('M d, Y'),
            ];
        }

        // Score entry progress (current month)
        $scoreProgress = null;
        if ($classes->isNotEmpty()) {
            $firstClass = $classes->first();
            $currentAcademicMonth = $this->getCurrentAcademicMonth();

            if ($currentAcademicMonth) {
                $subjectCount = Subject::where('grade_id', $firstClass->grade_id)->count();
                $enrollmentIds = \App\Models\Enrollment::where('class_id', $firstClass->id)
                    ->where('status', 'active')
                    ->pluck('id');

                $totalCells = $subjectCount * $enrollmentIds->count();

                $filledCells = MonthlyScore::whereIn('enrollment_id', $enrollmentIds)
                    ->where('academic_year_id', $firstClass->academic_year_id)
                    ->where('month', $currentAcademicMonth)
                    ->whereNotNull('score')
                    ->count();

                $percent = $totalCells > 0 ? round(($filledCells / $totalCells) * 100) : 0;

                $scoreProgress = [
                    'month'        => $currentAcademicMonth,
                    'month_name'   => \App\Helpers\AcademicCalendar::monthName($currentAcademicMonth, 'en'),
                    'filled'       => $filledCells,
                    'total'        => $totalCells,
                    'percent'      => $percent,
                    'class'        => $firstClass,
                ];
            }
        }

        return view('teacher.dashboard', compact(
            'classes',
            'recentSessions',
            'todayAttendance',
            'scoreProgress',
            'totalStudents',
            'totalSessions'
        ));
    }

    /**
     * Convert real calendar month to academic month
     * e.g., October (10) → academic month 1
     */
    private function getCurrentAcademicMonth(): ?int
    {
        $realMonth = (int) now()->format('n');

        $map = [
            10 => 1,  // October
            11 => 2,  // November
            12 => 3,  // December
            1  => 4,  // January
            2  => 5,  // February
            3  => 6,  // March
            4  => 7,  // April
            5  => 8,  // May
            6  => 9,  // June
            7  => 10, // July
            8  => 11, // August
            9  => 12, // September
        ];

        return $map[$realMonth] ?? null;
    }
}