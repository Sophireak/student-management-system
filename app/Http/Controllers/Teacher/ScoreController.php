<?php

namespace App\Http\Controllers\Teacher;

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
     |  ACCESS CONTROL
     |============================================================*/
    private function getAssignedClasses()
    {
        return auth()->user()->teacher
            ->classes()
            ->with('grade', 'academicYear')
            ->orderBy('name')
            ->get();
    }

    private function authorizeClass(SchoolClass $class): void
    {
        $isAssigned = auth()->user()->teacher
            ->classes()
            ->where('classes.id', $class->id)
            ->exists();

        if (! $isAssigned) abort(403, 'You are not assigned to this class.');
    }

    /* ============================================================
     |  DASHBOARD
     |============================================================*/
    public function index(Request $request): View
    {
        $classes = $this->getAssignedClasses();

        $classId        = $request->query('class_id');
        $selectedPeriod = $request->query('period');

        if (! $classId || ! $selectedPeriod) {
            return view('teacher.scores.index', [
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
        $this->authorizeClass($class);

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

        return view('teacher.scores.index', [
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
     |  INPUT
     |============================================================*/
    public function input(Request $request): View
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'period'     => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $class = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $this->authorizeClass($class);

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

        return view('teacher.scores.input', [
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

        $class = SchoolClass::findOrFail($request->class_id);
        $this->authorizeClass($class);

        $subject = Subject::findOrFail($request->subject_id);
        [$periodType, $periodValue] = $this->parsePeriod($request->period);

        if ($this->isLocked($class, $periodType, $periodValue)) {
            return back()->with('error', 'This period is locked by admin. Cannot save.');
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

        if ($request->boolean('save_and_next') && $request->filled('next_subject_id')) {
            return redirect()->route('teacher.scores.input', [
                'class_id'   => $class->id,
                'period'     => $request->period,
                'subject_id' => $request->next_subject_id,
            ])->with('success', 'Scores saved.');
        }

        return back()->with('success', 'Scores saved successfully.');
    }

    /* ============================================================
     |  REPORT
     |============================================================*/
    public function report(Request $request): View
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'period'   => 'required|string',
        ]);

        $class = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $this->authorizeClass($class);

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

        $matrix  = $this->buildScoreMatrix($class, $periodType, $periodValue, $subjects, $enrollments);
        $summary = $this->calculateSummary($enrollments, $subjects, $matrix);

        return view('teacher.scores.report', [
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

    // Only count rows that have actual data (score OR grade OR pass_fail)
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

        // Calculate rank
        $ranked = collect($summary)
            ->filter(fn ($s) => $s['average'] !== null)
            ->sortByDesc('average')
            ->keys();

        foreach ($ranked as $index => $enrollmentId) {
            $summary[$enrollmentId]['rank'] = $index + 1;
        }

        return $summary;
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
}