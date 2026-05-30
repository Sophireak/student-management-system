<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveSemesterReportRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\SemesterReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemesterReportController extends Controller
{
    public function __construct(private SemesterReportService $service) {}

    public function index(): View
    {
        return view('admin.semester-report.index',
                    $this->service->getFilterData());
    }

    public function show(Request $request): View
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester'         => ['required', 'integer', 'in:1,2'],
        ]);

        $class        = SchoolClass::with('grade', 'academicYear')
                                   ->findOrFail($request->class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);

        $sheet = $this->service->buildSheet(
            $class,
            (int) $request->semester,
            (int) $request->academic_year_id
        );

        return view('admin.semester-report.sheet', array_merge(
            $sheet,
            $this->service->getFilterData(),
            ['academicYear' => $academicYear]
        ));
    }

    public function calculate(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester'         => ['required', 'integer', 'in:1,2'],
        ]);

        $class = SchoolClass::findOrFail($request->class_id);

        $result = $this->service->calculateFromMonthly(
            $class,
            (int) $request->semester,
            (int) $request->academic_year_id,
            auth()->id()
        );

        return redirect()->route('admin.semester-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'semester'         => $request->semester,
        ])->with('success',
            "Semester scores calculated from monthly data. " .
            "{$result['processed']} records processed."
        );
    }

    public function save(SaveSemesterReportRequest $request): RedirectResponse
    {
        $result = $this->service->saveSheet(
            $request->class_id,
            $request->academic_year_id,
            $request->semester,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('admin.semester-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'semester'         => $request->semester,
        ])->with('success', "{$result['saved']} score(s) saved.");
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester'         => ['required', 'integer', 'in:1,2'],
        ]);

        $this->service->lockReport(
            $request->class_id,
            $request->academic_year_id,
            $request->semester,
            auth()->id()
        );

        return redirect()->route('admin.semester-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'semester'         => $request->semester,
        ])->with('success', 'Semester report locked.');
    }

    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester'         => ['required', 'integer', 'in:1,2'],
        ]);

        $this->service->unlockReport(
            $request->class_id,
            $request->academic_year_id,
            $request->semester
        );

        return redirect()->route('admin.semester-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'semester'         => $request->semester,
        ])->with('success', 'Semester report unlocked.');
    }
}