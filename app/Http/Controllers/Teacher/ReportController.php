<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Services\ReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    private function authorizeClass(SchoolClass $class): void
    {
        $assigned = $this->getTeacher()
                         ->classes()
                         ->where('classes.id', $class->id)
                         ->exists();

        if (! $assigned) abort(403);
    }

    public function index(): View
    {
        $teacher  = $this->getTeacher();

        // Teacher only sees their own classes
        $classes  = $teacher->classes()
                            ->with(['grade', 'academicYear'])
                            ->whereHas('academicYear', fn($q) =>
                                $q->where('is_active', true)
                            )
                            ->withCount([
                                'enrollments as total_students' => fn($q) =>
                                    $q->where('status', 'active'),
                                'attendanceSessions as total_sessions',
                            ])
                            ->get();

        return view('teacher.reports.index', compact('classes'));
    }

    public function class(SchoolClass $class): View
    {
        $this->authorizeClass($class);

        $report = $this->reports->classReport($class);

        return view('teacher.reports.class', $report);
    }

    public function classAttendance(SchoolClass $class): View
    {
        $this->authorizeClass($class);

        $report = $this->reports->classAttendanceReport($class);

        return view('teacher.reports.class-attendance', $report);
    }
}