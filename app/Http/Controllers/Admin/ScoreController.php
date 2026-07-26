<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\MonthlyReportLock;
use App\Models\MonthlyScore;
use App\Models\SchoolClass;
use App\Models\SemesterReportLock;
use App\Models\SemesterScore;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScoreController extends Controller
{
    /* ============================================================
     |  DASHBOARD
     |============================================================*/
    public function index(Request $request): View
    {
        $classes = SchoolClass::with('grade', 'academicYear')
            ->orderBy('name')
            ->get();

        $classId        = $request->query('class_id');
        $selectedPeriod = $request->query('period');

        // No filter yet — show empty state
        if (! $classId || ! $selectedPeriod) {
            return view('admin.scores.index', [
                'classes'         => $classes,
                'class'           => null,
                'subjects'        => collect(),
                'selectedPeriod'  => null,
                'periodLabel'     => null,
                'totalStudents'   => 0,
                'completionMap'   => [],
                'overallProgress' => 0,
                'isLocked'        => false,
            ]);
        }

        $class = SchoolClass::with('grade', 'academicYear')->findOrFail($classId);
        [$periodType, $periodValue] = $this->parsePeriod($selectedPeriod);

        $subjects = Subject::where('grade_id', $class->grade_id)
            ->orderBy('name')
            ->get();

        $enrollments = Enrollment::where('class_id', $class->id)
            ->where('status', 'active')
            ->get();

        $totalStudents = $enrollments->count();

        $completionMap = $this->buildCompletionMap(
            $class, $periodType, $periodValue, $subjects, $enrollments
        );

        $totalCells      = $subjects->count() * $totalStudents;
        $filledCells     = collect($completionMap)->sum('completed');
        $overallProgress = $totalCells > 0 ? (int) round(($filledCells / $totalCells) * 100) : 0;

        return view('admin.scores.index', [
            'classes'         => $classes,
            'class'           => $class,
            'subjects'        => $subjects,
            'selectedPeriod'  => $selectedPeriod,
            'periodLabel'     => $this->periodLabel($periodType, $periodValue),
            'totalStudents'   => $totalStudents,
            'completionMap'   => $completionMap,
            'overallProgress' => $overallProgress,
            'isLocked'        => $this->isLocked($class, $periodType, $periodValue),
        ]);
    }

    /* ============================================================
     |  INPUT SCREEN
     |============================================================*/
    public function input(Request $request): View
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'period'     => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $class   = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);
        [$periodType, $periodValue] = $this->parsePeriod($request->period);

        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->get()
            ->sortBy(fn ($e) => $e->student->full_name ?? '')
            ->values();

        $scores = $this->fetchScores($class, $periodType, $periodValue, $subject->id, $enrollments);

        $allSubjects = Subject::where('grade_id', $class->grade_id)
            ->orderBy('name')
            ->get();

        $currentIndex = $allSubjects->search(fn ($s) => $s->id === $subject->id);
        $prevSubject  = $currentIndex > 0 ? $allSubjects[$currentIndex - 1] : null;
        $nextSubject  = $currentIndex < $allSubjects->count() - 1 ? $allSubjects[$currentIndex + 1] : null;

        return view('admin.scores.input', [
            'class'          => $class,
            'subject'        => $subject,
            'enrollments'    => $enrollments,
            'scores'         => $scores,
            'selectedPeriod' => $request->period,
            'periodLabel'    => $this->periodLabel($periodType, $periodValue),
            'prevSubject'    => $prevSubject,
            'nextSubject'    => $nextSubject,
            'isLocked'       => $this->isLocked($class, $periodType, $periodValue),
        ]);
    }

    /* ============================================================
     |  SAVE
     |============================================================*/
    public function save(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'period'     => 'required|string',
            'scores'     => 'array',
        ]);

        $class   = SchoolClass::findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);
        [$periodType, $periodValue] = $this->parsePeriod($request->period);

        if ($this->isLocked($class, $periodType, $periodValue)) {
            return back()->with('error', 'This period is locked. Cannot save.');
        }

        DB::transaction(function () use ($request, $class, $subject, $periodType, $periodValue) {
            foreach ($request->input('scores', []) as $entry) {
                if (empty($entry['enrollment_id'])) continue;

                $data = $this->extractScoreValues($entry, $subject);
                if ($data === null) continue;

                if ($periodType === 'month') {
                    MonthlyScore::updateOrCreate(
                        [
                            'enrollment_id'    => $entry['enrollment_id'],
                            'subject_id'       => $subject->id,
                            'academic_year_id' => $class->academic_year_id,
                            'month'            => $periodValue,
                        ],
                        array_merge($data, ['entered_by' => auth()->id()])
                    );
                } else {
                    SemesterScore::updateOrCreate(
                        [
                            'enrollment_id'    => $entry['enrollment_id'],
                            'subject_id'       => $subject->id,
                            'academic_year_id' => $class->academic_year_id,
                            'semester'         => $periodValue,
                        ],
                        array_merge($data, ['entered_by' => auth()->id()])
                    );
                }
            }
        });

        // Save & Next
        if ($request->boolean('save_and_next') && $request->filled('next_subject_id')) {
            return redirect()->route('admin.scores.input', [
                'class_id'   => $class->id,
                'period'     => $request->period,
                'subject_id' => $request->next_subject_id,
            ])->with('success', 'Scores saved.');
        }

        return back()->with('success', 'Scores saved successfully.');
    }

    /* ============================================================
     |  LOCK / UNLOCK
     |============================================================*/
    public function lock(Request $request): RedirectResponse
    {
        $data = $this->lockRequestData($request);

        $lockModel = $data['periodType'] === 'month' ? MonthlyReportLock::class : SemesterReportLock::class;
        $key       = $data['periodType'] === 'month' ? 'month' : 'semester';

        $lockModel::updateOrCreate(
            [
                'class_id'         => $data['class']->id,
                'academic_year_id' => $data['class']->academic_year_id,
                $key               => $data['periodValue'],
            ],
            [
                'locked_by' => auth()->id(),
                'locked_at' => now(),
            ]
        );

        return back()->with('success', 'Sheet locked.');
    }

    public function unlock(Request $request): RedirectResponse
    {
        $data = $this->lockRequestData($request);

        $lockModel = $data['periodType'] === 'month' ? MonthlyReportLock::class : SemesterReportLock::class;
        $key       = $data['periodType'] === 'month' ? 'month' : 'semester';

        $lockModel::where('class_id', $data['class']->id)
            ->where('academic_year_id', $data['class']->academic_year_id)
            ->where($key, $data['periodValue'])
            ->delete();

        return back()->with('success', 'Sheet unlocked.');
    }

    /* ============================================================
     |  HELPERS
     |============================================================*/
    private function parsePeriod(string $period): array
    {
        if (str_starts_with($period, 'month_')) {
            return ['month', (int) str_replace('month_', '', $period)];
        }
        if (str_starts_with($period, 'semester_')) {
            return ['semester', (int) str_replace('semester_', '', $period)];
        }
        abort(422, 'Invalid period.');
    }

    private function periodLabel(string $type, int $value): string
    {
        if ($type === 'month') {
            return 'Month ' . $value . ' — ' . MonthlyScore::monthName($value);
        }
        return SemesterScore::semesterLabel($value);
    }

    private function isLocked(SchoolClass $class, string $periodType, int $periodValue): bool
    {
        if ($periodType === 'month') {
            return MonthlyReportLock::where('class_id', $class->id)
                ->where('academic_year_id', $class->academic_year_id)
                ->where('month', $periodValue)
                ->exists();
        }
        return SemesterReportLock::where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->where('semester', $periodValue)
            ->exists();
    }

    private function lockRequestData(Request $request): array
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'period'   => 'required|string',
        ]);

        $class = SchoolClass::findOrFail($request->class_id);
        [$periodType, $periodValue] = $this->parsePeriod($request->period);

        return compact('class', 'periodType', 'periodValue');
    }

    private function fetchScores(SchoolClass $class, string $type, int $value, int $subjectId, $enrollments)
    {
        $enrollmentIds = $enrollments->pluck('id');
        if ($enrollmentIds->isEmpty()) return collect();

        $query = $type === 'month'
            ? MonthlyScore::where('month', $value)
            : SemesterScore::where('semester', $value);

        return $query->where('academic_year_id', $class->academic_year_id)
            ->where('subject_id', $subjectId)
            ->whereIn('enrollment_id', $enrollmentIds)
            ->get()
            ->keyBy('enrollment_id');
    }

    private function buildCompletionMap(SchoolClass $class, string $type, int $value, $subjects, $enrollments): array
{
    $enrollmentIds = $enrollments->pluck('id');
    if ($enrollmentIds->isEmpty()) return [];

    $query = $type === 'month'
        ? MonthlyScore::where('month', $value)
        : SemesterScore::where('semester', $value);

    $counts = $query->where('academic_year_id', $class->academic_year_id)
        ->whereIn('enrollment_id', $enrollmentIds)
        ->where(function ($q) {
            $q->whereNotNull('score')
              ->orWhereNotNull('grade')
              ->orWhereNotNull('pass_fail');
        })
        ->select('subject_id', DB::raw('COUNT(*) as total'))
        ->groupBy('subject_id')
        ->pluck('total', 'subject_id');

    $map = [];
    foreach ($subjects as $subject) {
        $map[$subject->id] = ['completed' => (int) ($counts[$subject->id] ?? 0)];
    }
    return $map;
}

    private function extractScoreValues(array $entry, Subject $subject): ?array
    {
        if ($subject->isNumeric()) {
            $val = $entry['score'] ?? null;
            if ($val === '' || $val === null) return null;
            return ['score' => $val, 'grade' => null, 'pass_fail' => null];
        }
        if ($subject->isGrade()) {
            $val = $entry['grade'] ?? null;
            if (! $val) return null;
            return ['score' => null, 'grade' => $val, 'pass_fail' => null];
        }
        $val = $entry['pass_fail'] ?? null;
        if (! $val) return null;
        return ['score' => null, 'grade' => null, 'pass_fail' => $val];
    }

    /* ============================================================
 |  REPORT (Official Khmer standard class report)
 |============================================================*/
public function report(Request $request): View
{
    $request->validate([
        'class_id' => 'required|exists:classes,id',
        'period'   => 'required|string',
    ]);

    $class = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
    [$periodType, $periodValue] = $this->parsePeriod($request->period);

    $subjects = Subject::where('grade_id', $class->grade_id)
        ->orderBy('name')
        ->get();

    $enrollments = Enrollment::with('student')
        ->where('class_id', $class->id)
        ->where('status', 'active')
        ->get()
        ->sortBy(fn ($e) => $e->student->full_name ?? '')
        ->values();

    // Build score matrix: [enrollment_id][subject_id] = score record
    $matrix = $this->buildScoreMatrix($class, $periodType, $periodValue, $subjects, $enrollments);

    // Calculate totals, averages, and ranks
    $summary = $this->calculateSummary($enrollments, $subjects, $matrix);

    return view('admin.scores.report', [
        'class'          => $class,
        'subjects'       => $subjects,
        'enrollments'    => $enrollments,
        'matrix'         => $matrix,
        'summary'        => $summary,
        'selectedPeriod' => $request->period,
        'periodLabel'    => $this->periodLabel($periodType, $periodValue),
        'isLocked'       => $this->isLocked($class, $periodType, $periodValue),
    ]);
}

/* ============================================================
 |  Build score matrix
 |============================================================*/
private function buildScoreMatrix(SchoolClass $class, string $type, int $value, $subjects, $enrollments): array
{
    $enrollmentIds = $enrollments->pluck('id');
    if ($enrollmentIds->isEmpty()) return [];

    $query = $type === 'month'
        ? MonthlyScore::where('month', $value)
        : SemesterScore::where('semester', $value);

    $scores = $query->where('academic_year_id', $class->academic_year_id)
        ->whereIn('enrollment_id', $enrollmentIds)
        ->get();

    $matrix = [];
    foreach ($scores as $score) {
        $matrix[$score->enrollment_id][$score->subject_id] = $score;
    }
    return $matrix;
}

/* ============================================================
 |  Calculate summary (total, average, rank)
 |============================================================*/
private function calculateSummary($enrollments, $subjects, array $matrix): array
{
    $summary = [];

    foreach ($enrollments as $enrollment) {
        $total = 0;
        $count = 0;

        foreach ($subjects as $subject) {
            $score = $matrix[$enrollment->id][$subject->id] ?? null;
            if ($score && $score->score !== null) {
                $total += (float) $score->score;
                $count++;
            }
        }

        $summary[$enrollment->id] = [
            'total'   => $count > 0 ? round($total, 2) : null,
            'average' => $count > 0 ? round($total / $count, 2) : null,
            'count'   => $count,
            'rank'    => null,
        ];
    }

    // Calculate rank (better version)
    $ranked = collect($summary)
        ->filter(fn ($s) => $s['average'] !== null)
        ->sortByDesc('average')
        ->keys();

    foreach ($ranked as $index => $enrollmentId) {
        $summary[$enrollmentId]['rank'] = $index + 1;
    }

    return $summary;
}
}