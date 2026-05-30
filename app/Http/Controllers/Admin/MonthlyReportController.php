<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveMonthlyReportRequest;
use App\Models\AcademicYear;
use App\Models\MonthlyReportLock;
use App\Models\MonthlyScore;
use App\Models\SchoolClass;
use App\Services\MonthlyReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyReportController extends Controller
{
    public function __construct(private MonthlyReportService $service) {}

    public function index(Request $request): View
    {
        $filterData = $this->service->getFilterData();

        return view('admin.monthly-report.index', $filterData);
    }

    public function show(Request $request): View
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'month'            => ['required', 'integer', 'min:1', 'max:9'],
        ]);

        $class       = SchoolClass::with('grade', 'academicYear')
            ->findOrFail($request->class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);

        $sheet = $this->service->buildSheet(
            $class,
            (int) $request->month,
            (int) $request->academic_year_id
        );

        $filterData = $this->service->getFilterData();

        return view('admin.monthly-report.sheet', array_merge(
            $sheet,
            $filterData,
            ['academicYear' => $academicYear]
        ));
    }

    public function save(SaveMonthlyReportRequest $request): RedirectResponse
    {
        $result = $this->service->saveSheet(
            $request->class_id,
            $request->academic_year_id,
            $request->month,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('admin.monthly-report.show', [
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

    public function lock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'month'            => ['required', 'integer', 'min:1', 'max:9'],
        ]);

        $this->service->lockReport(
            $request->class_id,
            $request->academic_year_id,
            $request->month,
            auth()->id()
        );

        return redirect()->route('admin.monthly-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'month'            => $request->month,
        ])->with('success', 'Monthly report locked successfully.');
    }

    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'month'            => ['required', 'integer', 'min:1', 'max:9'],
        ]);

        $this->service->unlockReport(
            $request->class_id,
            $request->academic_year_id,
            $request->month
        );

        return redirect()->route('admin.monthly-report.show', [
            'class_id'         => $request->class_id,
            'academic_year_id' => $request->academic_year_id,
            'month'            => $request->month,
        ])->with('success', 'Monthly report unlocked.');
    }
}
