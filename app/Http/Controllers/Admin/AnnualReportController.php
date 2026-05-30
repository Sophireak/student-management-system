<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAnnualReportRequest;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\AnnualReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnualReportController extends Controller
{
    public function __construct(private AnnualReportService $service) {}

    public function index(): View
    {
        return view('admin.annual-report.index',
                    $this->service->getFilterData());
    }

    public function show(Request $request): View
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $class        = SchoolClass::with('grade', 'academicYear')
                                   ->findOrFail($request->class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);

        $sheet = $this->service->buildSheet(
            $class,
            (int) $request->academic_year_id
        );

        return view('admin.annual-report.sheet', array_merge(
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
        ]);

        $class  = SchoolClass::findOrFail($request->class_id);
        $result = $this->service->calculateFromSemesters(
            $class,
            (int) $request->academic_year_id,
            auth()->id()
        );

        return redirect()->route('admin.annual-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
        ])->with('success',
            "Annual scores calculated. {$result['processed']} students processed."
        );
    }

    public function save(SaveAnnualReportRequest $request): RedirectResponse
    {
        $result = $this->service->saveSheet(
            $request->class_id,
            $request->academic_year_id,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('admin.annual-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
        ])->with('success', "{$result['saved']} record(s) saved.");
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $this->service->lockReport(
            $request->class_id,
            $request->academic_year_id,
            auth()->id()
        );

        return redirect()->route('admin.annual-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
        ])->with('success', 'Annual report locked successfully.');
    }

    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $this->service->unlockReport(
            $request->class_id,
            $request->academic_year_id
        );

        return redirect()->route('admin.annual-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
        ])->with('success', 'Annual report unlocked.');
    }
}