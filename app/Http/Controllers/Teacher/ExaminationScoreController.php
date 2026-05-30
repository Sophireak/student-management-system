<?php

namespace App\Http\Controllers\Teacher;

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
        $teacher    = $this->getTeacher();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('classTeachers', fn($q) => $q->where('teacher_id', $teacher->id))
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

        $this->authorizeClass($request->class_id);

        $p          = $this->parsePeriod($request->period);
        $teacher    = $this->getTeacher();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();
        $class      = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('classTeachers', fn($q) => $q->where('teacher_id', $teacher->id))
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $selectedPeriod = $request->period;

        if ($p['type'] === 'month') {
            $sheet = $this->monthly->buildSheet($class, $p['value'], $activeYear->id);
            return view('examination-scores.monthly-sheet', array_merge(
                $sheet,
                compact('activeYear', 'classes', 'selectedPeriod'),
                ['routePrefix' => 'teacher', 'academicYear' => $activeYear]
            ));
        }

        $sheet = $this->semester->buildSheet($class, $p['value'], $activeYear->id);
        return view('examination-scores.semester-sheet', array_merge(
            $sheet,
            compact('activeYear', 'classes', 'selectedPeriod'),
            ['routePrefix' => 'teacher', 'academicYear' => $activeYear]
        ));
    }

    public function saveMonthly(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'month'    => ['required', 'integer', 'min:1', 'max:9'],
            'scores'   => ['required', 'array'],
        ]);

        $this->authorizeClass($request->class_id);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $result = $this->monthly->saveSheet(
            $request->class_id,
            $activeYear->id,
            $request->month,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('teacher.examination-scores.sheet', [
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

        $this->authorizeClass($request->class_id);

        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $result = $this->semester->saveSheet(
            $request->class_id,
            $activeYear->id,
            $request->semester,
            $request->scores,
            auth()->id()
        );

        return redirect()->route('teacher.examination-scores.sheet', [
            'class_id' => $request->class_id,
            'period'   => 'semester_' . $request->semester,
        ])->with('success', "{$result['saved']} score(s) saved." .
            ($result['skipped'] > 0 ? " {$result['skipped']} empty cell(s) skipped." : ''));
    }
}