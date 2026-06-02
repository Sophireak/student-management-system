<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\MonthlyReportService;
use App\Services\SemesterReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExaminationScoreController extends Controller
{
    public function __construct(
        private MonthlyReportService $monthly,
        private SemesterReportService $semester,
    ) {}

    public function index(): View
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $classes       = SchoolClass::with(['grade', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        return view('examination-scores.index', compact('academicYears', 'classes'));
    }

    public function sheet(Request $request): View
    {
        $request->validate([
            'class_id'    => ['required', 'exists:classes,id'],
            'period'      => ['required', 'string'],
        ]);

        // Parse period: "month_3" or "semester_1"
        [$type, $value] = explode('_', $request->period, 2);

        $class        = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $activeYear   = AcademicYear::where('is_active', true)->firstOrFail();

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $selectedPeriod = $request->period;

        if ($type === 'month') {
            $sheet = $this->monthly->buildSheet($class, (int) $value, $activeYear->id);
            return view('examination-scores.monthly-sheet', array_merge(
                $sheet,
                compact('activeYear', 'classes', 'selectedPeriod'),
                ['academicYear' => $activeYear]
            ));
        }

        $sheet = $this->semester->buildSheet($class, (int) $value, $activeYear->id);
        return view('examination-scores.semester-sheet', array_merge(
            $sheet,
            compact('activeYear', 'classes', 'selectedPeriod'),
            ['academicYear' => $activeYear]
        ));
    }

    public function saveMonthly(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'month'    => ['required', 'integer', 'min:1', 'max:9'],
            'scores'   => ['required', 'array'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $result = $this->monthly->saveSheet(
            $request->class_id,
            $activeYear->id,
            $request->month,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('admin.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'month_' . $request->month,
        ])->with('success', "{$result['saved']} score(s) saved." .
            ($result['skipped'] > 0 ? " {$result['skipped']} empty cell(s) skipped." : ''));
    }

    public function saveSemester(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'semester' => ['required', 'integer', 'in:1,2'],
            'scores'   => ['required', 'array'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $result = $this->semester->saveSheet(
            $request->class_id,
            $activeYear->id,
            $request->semester,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('admin.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'semester_' . $request->semester,
        ])->with('success', "{$result['saved']} score(s) saved." .
            ($result['skipped'] > 0 ? " {$result['skipped']} empty cell(s) skipped." : ''));
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'period_type'      => ['required', 'in:month,semester'],
            'month'            => ['required_if:period_type,month', 'nullable', 'integer', 'min:1', 'max:9'],
            'semester'         => ['required_if:period_type,semester', 'nullable', 'integer', 'in:1,2'],
        ]);

        if ($request->period_type === 'month') {
            $this->monthly->lockReport(
                $request->class_id,
                $request->academic_year_id,
                $request->month,
                auth()->id()
            );
        } else {
            $this->semester->lockReport(
                $request->class_id,
                $request->academic_year_id,
                $request->semester,
                auth()->id()
            );
        }

        return redirect()->back()->with('success', 'Score sheet locked successfully.');
    }

    public function unlock(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'period_type'      => ['required', 'in:month,semester'],
            'month'            => ['required_if:period_type,month', 'nullable', 'integer', 'min:1', 'max:9'],
            'semester'         => ['required_if:period_type,semester', 'nullable', 'integer', 'in:1,2'],
        ]);

        if ($request->period_type === 'month') {
            $this->monthly->unlockReport(
                $request->class_id,
                $request->academic_year_id,
                $request->month
            );
        } else {
            $this->semester->unlockReport(
                $request->class_id,
                $request->academic_year_id,
                $request->semester
            );
        }

        return redirect()->back()->with('success', 'Score sheet unlocked successfully.');
    }
}