<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\MonthlyScore;
use App\Models\SchoolClass;
use App\Models\SemesterScore;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
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
     |  INDEX — Filter page
     |============================================================*/
    public function index(): View
    {
        $classes       = $this->getAssignedClasses();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('teacher.reports.index', [
            'classes'       => $classes,
            'academicYears' => $academicYears,
        ]);
    }

    /* ============================================================
     |  PRINT — Generate report based on type
     |============================================================*/
    public function print(Request $request): View
    {
        $request->validate([
            'report'   => 'required|in:score-list,ranking,honor',
            'class_id' => 'required|exists:classes,id',
            'period'   => 'required|in:monthly,semester,annual',
            'month'    => 'required_if:period,monthly|nullable|integer|between:1,12',
            'semester' => 'required_if:period,semester|nullable|integer|in:1,2',
        ]);

        $class = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $this->authorizeClass($class);

        $reportType  = $request->report;
        $period      = $request->period;
        $periodValue = $period === 'monthly' ? $request->month : ($period === 'semester' ? $request->semester : null);

        $subjects = Subject::where('grade_id', $class->grade_id)
            ->orderBy('name')
            ->get();

        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->get()
            ->sortBy(fn ($e) => $e->student->full_name ?? '')
            ->values();

        $matrix     = $this->buildScoreMatrix($class, $period, $periodValue, $subjects, $enrollments);
        $summary    = $this->calculateSummary($enrollments, $subjects, $matrix);
        $statistics = $this->calculateStatistics($enrollments, $summary);
        $periodLabel = $this->periodLabel($period, $periodValue);

        return view('teacher.reports.print', [
            'reportType'  => $reportType,
            'class'       => $class,
            'subjects'    => $subjects,
            'enrollments' => $enrollments,
            'matrix'      => $matrix,
            'summary'     => $summary,
            'statistics'  => $statistics,
            'period'      => $period,
            'periodValue' => $periodValue,
            'periodLabel' => $periodLabel,
            'academicYear' => $class->academicYear,
        ]);
    }

    /* ============================================================
     |  HELPERS (same as admin)
     |============================================================*/
    private function periodLabel(string $period, ?int $value): string
{
    if ($period === 'monthly') {
        return 'ខែ ' . \App\Helpers\AcademicCalendar::monthName($value, 'kh');
    }
    if ($period === 'semester') {
        return $value === 1 ? 'ឆមាសទី ១' : 'ឆមាសទី ២';
    }
    return 'ប្រចាំឆ្នាំ';
}

    private function buildScoreMatrix(SchoolClass $class, string $period, ?int $value, $subjects, $enrollments): array
    {
        $enrollmentIds = $enrollments->pluck('id');
        if ($enrollmentIds->isEmpty()) return [];

        $query = $period === 'monthly'
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

        $ranked = collect($summary)
            ->filter(fn ($s) => $s['average'] !== null)
            ->sortByDesc('average')
            ->keys();

        foreach ($ranked as $index => $enrollmentId) {
            $summary[$enrollmentId]['rank'] = $index + 1;
        }

        return $summary;
    }

    private function calculateStatistics($enrollments, array $summary): array
    {
        $total   = $enrollments->count();
        $females = $enrollments->filter(fn ($e) => strtolower($e->student->gender ?? '') === 'female')->count();
        $males   = $total - $females;

        $gradeCounts = [
            'excellent' => 0, 'excellent_female' => 0,
            'very_good' => 0, 'very_good_female' => 0,
            'good'      => 0, 'good_female'      => 0,
            'average'   => 0, 'average_female'   => 0,
            'weak'      => 0, 'weak_female'      => 0,
            'fail'      => 0, 'fail_female'      => 0,
        ];

        $totalWithScores = 0;
        foreach ($enrollments as $enrollment) {
            $avg = $summary[$enrollment->id]['average'] ?? null;
            if ($avg === null) continue;
            $totalWithScores++;

            $isFemale = strtolower($enrollment->student->gender ?? '') === 'female';

            $key = match(true) {
                $avg >= 9.00 => 'excellent',
                $avg >= 8.00 => 'very_good',
                $avg >= 7.00 => 'good',
                $avg >= 6.00 => 'average',
                $avg >= 5.00 => 'weak',
                default      => 'fail',
            };

            $gradeCounts[$key]++;
            if ($isFemale) $gradeCounts[$key . '_female']++;
        }

        $passCount  = $gradeCounts['excellent'] + $gradeCounts['very_good'] + $gradeCounts['good'] + $gradeCounts['average'];
        $passFemale = $gradeCounts['excellent_female'] + $gradeCounts['very_good_female'] + $gradeCounts['good_female'] + $gradeCounts['average_female'];
        $failCount  = $gradeCounts['weak'] + $gradeCounts['fail'];
        $failFemale = $gradeCounts['weak_female'] + $gradeCounts['fail_female'];

        return [
            'total'        => $total,
            'females'      => $females,
            'males'        => $males,
            'total_scored' => $totalWithScores,
            'grade_counts' => $gradeCounts,
            'pass_count'   => $passCount,
            'pass_female'  => $passFemale,
            'pass_percent' => $totalWithScores > 0 ? round(($passCount / $totalWithScores) * 100, 2) : 0,
            'fail_count'   => $failCount,
            'fail_female'  => $failFemale,
            'fail_percent' => $totalWithScores > 0 ? round(($failCount / $totalWithScores) * 100, 2) : 0,
        ];
    }
}