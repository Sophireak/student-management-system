<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\SemesterReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemesterReportController extends Controller
{
    public function __construct(private SemesterReportService $service) {}

    private function getTeacher()
    {
        $teacher = auth()->user()->teacher;
        if (! $teacher) abort(403, 'Teacher profile not found.');
        return $teacher;
    }

    private function authorizeClass(int $classId): void
    {
        $assigned = $this->getTeacher()
                         ->classes()
                         ->where('classes.id', $classId)
                         ->exists();
        if (! $assigned) abort(403);
    }

    public function index(): View
    {
        $teacher = $this->getTeacher();
        return view('teacher.semester-report.index',
                    $this->service->getTeacherFilterData($teacher->id));
    }

    public function show(Request $request): View
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'semester' => ['required', 'integer', 'in:1,2'],
        ]);

        $this->authorizeClass($request->class_id);

        $activeYear   = AcademicYear::where('is_active', true)->firstOrFail();
        $class        = SchoolClass::with('grade', 'academicYear')
                                   ->findOrFail($request->class_id);
        $teacher      = $this->getTeacher();

        $sheet = $this->service->buildSheet(
            $class,
            (int) $request->semester,
            $activeYear->id
        );

        return view('teacher.semester-report.sheet', array_merge(
            $sheet,
            $this->service->getTeacherFilterData($teacher->id),
            ['academicYear' => $activeYear]
        ));
    }
}