<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(): View
    {
        $overview = $this->reports->attendanceOverview();

        return view('admin.reports.index', compact('overview'));
    }

    public function student(Request $request, Student $student): View
    {
        // Admin selects which enrollment (class + year) to report on
        $student->load('enrollments.schoolClass.academicYear');

        $enrollments = $student->enrollments()
            ->with(['schoolClass.academicYear', 'schoolClass.grade'])
            ->latest()
            ->get();

        // Default to most recent enrollment or admin-selected one
        $selectedId  = $request->integer('enrollment_id')
            ?: $enrollments->first()?->id;

        $enrollment  = $enrollments->firstWhere('id', $selectedId);

        if (! $enrollment) {
            return view('admin.reports.student', [
                'student'     => $student,
                'enrollments' => $enrollments,
                'report'      => null,
                'selectedId'  => null,
            ]);
        }

        $report = $this->reports->studentReport($student, $enrollment);

        return view('admin.reports.student', array_merge($report, [
            'enrollments' => $enrollments,
            'selectedId'  => $selectedId,
        ]));
    }

    public function class(SchoolClass $class): View
    {
        $report = $this->reports->classReport($class);

        return view('admin.reports.class', $report);
    }

    public function attendance(): View
    {
        $overview = $this->reports->attendanceOverview();

        return view('admin.reports.attendance', compact('overview'));
    }

    public function classAttendance(SchoolClass $class): View
    {
        $report = $this->reports->classAttendanceReport($class);

        return view('admin.reports.class-attendance', $report);
    }
}