<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\MonthlyReportLock;
use App\Models\SemesterReportLock;
use App\Services\MonthlyReportService;
use App\Services\SemesterReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ExaminationScoreController extends Controller
{
    public function __construct(
        private MonthlyReportService $monthly,
        private SemesterReportService $semester,
    ) {}

    /** Parse "month_3" or "semester_2" into ['type'=>'month','value'=>3] */
    private function parsePeriod(?string $period): array
    {
        if (! $period || ! str_contains($period, '_')) {
            abort(422, 'Invalid period.');
        }
        [$type, $value] = explode('_', $period, 2);
        if (! in_array($type, ['month', 'semester'])) abort(422, 'Invalid period type.');
        return ['type' => $type, 'value' => (int) $value];
    }

    public function index(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Admin sees all classes from the active year only (same simplicity as teacher)
        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        return view('examination-scores.index', compact('classes', 'activeYear'));
    }

    public function sheet(Request $request): View
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'period'   => ['required', 'string'],
        ]);

        $p          = $this->parsePeriod($request->period);
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();
        $class      = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $selectedPeriod = $request->period;

        if ($p['type'] === 'month') {
            $isLocked = MonthlyReportLock::where('class_id', $class->id)
                ->where('academic_year_id', $activeYear->id)
                ->where('month', $p['value'])
                ->exists();

            $sheet = $this->monthly->buildSheet($class, $p['value'], $activeYear->id);
            return view('examination-scores.monthly-sheet', array_merge(
                $sheet,
                compact('activeYear', 'classes', 'selectedPeriod', 'isLocked'),
                ['routePrefix' => 'admin', 'academicYear' => $activeYear]
            ));
        }

        $isLocked = SemesterReportLock::where('class_id', $class->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('semester', $p['value'])
            ->exists();

        $sheet = $this->semester->buildSheet($class, $p['value'], $activeYear->id);
        return view('examination-scores.semester-sheet', array_merge(
            $sheet,
            compact('activeYear', 'classes', 'selectedPeriod', 'isLocked'),
            ['routePrefix' => 'admin', 'academicYear' => $activeYear]
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

    public function lockMonthly(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'month'    => ['required', 'integer', 'min:1', 'max:9'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        MonthlyReportLock::firstOrCreate(
            [
                'class_id'        => $request->class_id,
                'academic_year_id'=> $activeYear->id,
                'month'           => $request->month,
            ],
            [
                'locked_by' => auth()->id(),
                'locked_at' => now(),
            ]
        );

        return redirect()->route('admin.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'month_' . $request->month,
        ])->with('success', 'Report locked.');
    }

    public function unlockMonthly(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'month'    => ['required', 'integer', 'min:1', 'max:9'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        MonthlyReportLock::where('class_id', $request->class_id)
            ->where('academic_year_id', $activeYear->id)
            ->where('month', $request->month)
            ->delete();

        return redirect()->route('admin.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'month_' . $request->month,
        ])->with('success', 'Report unlocked.');
    }

    public function lockSemester(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'semester' => ['required', 'integer', 'in:1,2'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        SemesterReportLock::firstOrCreate(
            [
                'class_id'        => $request->class_id,
                'academic_year_id'=> $activeYear->id,
                'semester'        => $request->semester,
            ],
            [
                'locked_by' => auth()->id(),
                'locked_at' => now(),
            ]
        );

        return redirect()->route('admin.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'semester_' . $request->semester,
        ])->with('success', 'Report locked.');
    }

    public function unlockSemester(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'semester' => ['required', 'integer', 'in:1,2'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        SemesterReportLock::where('class_id', $request->class_id)
            ->where('academic_year_id', $activeYear->id)
            ->where('semester', $request->semester)
            ->delete();

        return redirect()->route('admin.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'semester_' . $request->semester,
        ])->with('success', 'Report unlocked.');
    }
}