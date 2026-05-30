<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\AnnualReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnualReportController extends Controller
{
    public function __construct(private AnnualReportService $service) {}

    private function getTeacher()
    {
        $teacher = auth()->user()->teacher;
        if (! $teacher) abort(403, 'Teacher profile not found.');
        return $teacher;
    }

    public function index(): View
    {
        $teacher = $this->getTeacher();
        return view('teacher.annual-report.index',
                    $this->service->getTeacherFilterData($teacher->id));
    }

    public function show(Request $request): View
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $teacher = $this->getTeacher();
        $assigned = $teacher->classes()
            ->where('classes.id', $request->class_id)
            ->exists();
        if (! $assigned) abort(403);

        $activeYear   = AcademicYear::where('is_active', true)->firstOrFail();
        $class        = SchoolClass::with('grade', 'academicYear')
                                   ->findOrFail($request->class_id);

        $sheet = $this->service->buildSheet($class, $activeYear->id);

        return view('teacher.annual-report.sheet', array_merge(
            $sheet,
            $this->service->getTeacherFilterData($teacher->id),
            ['academicYear' => $activeYear]
        ));
    }
}