<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveMonthlyReportRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\MonthlyReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyReportController extends Controller
{
    public function __construct(private MonthlyReportService $service) {}

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

        if (! $assigned) abort(403, 'You are not assigned to this class.');
    }

    public function index(): View
    {
        $teacher    = $this->getTeacher();
        $filterData = $this->service->getTeacherFilterData($teacher->id);

        return view('teacher.monthly-report.index', $filterData);
    }

    public function show(Request $request): View
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'month'            => ['required', 'integer', 'min:1', 'max:9'],
        ]);

        $this->authorizeClass($request->class_id);

        $class        = SchoolClass::with('grade', 'academicYear')
            ->findOrFail($request->class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $teacher      = $this->getTeacher();

        $sheet = $this->service->buildSheet(
            $class,
            (int) $request->month,
            (int) $request->academic_year_id
        );

        $filterData = $this->service->getTeacherFilterData($teacher->id);

        return view('teacher.monthly-report.sheet', array_merge(
            $sheet,
            $filterData,
            ['academicYear' => $academicYear]
        ));
    }

    public function save(SaveMonthlyReportRequest $request): RedirectResponse
    {
        $this->authorizeClass($request->class_id);

        $result = $this->service->saveSheet(
            $request->class_id,
            $request->academic_year_id,
            $request->month,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('teacher.monthly-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'month'            => $request->month,
        ])->with(
            'success',
            "{$result['saved']} score(s) saved." .
                ($result['skipped'] > 0
                    ? " {$result['skipped']} empty cell(s) skipped." : '')
        );
    }
}
