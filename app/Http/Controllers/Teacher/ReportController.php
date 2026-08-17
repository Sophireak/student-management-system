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

    // Auto-select if teacher has only 1 class
    $defaultClassId = $classes->count() === 1 ? $classes->first()->id : null;

    return view('teacher.reports.index', [
        'classes'        => $classes,
        'academicYears'  => $academicYears,
        'defaultClassId' => $defaultClassId,
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
            'report_date' => 'nullable|date',
        ]);

        $class = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $reportDate = $request->report_date 
    ? \Carbon\Carbon::parse($request->report_date) 
    : now();
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
        $attendanceCounts = $this->getAttendanceCounts($class, $period, $periodValue, $enrollments);
        $periodLabel = $this->periodLabel($period, $periodValue);

        return view('teacher.reports.print', [
    'reportType'       => $reportType,
    'reportDate'       => $reportDate,
    'class'            => $class,
    'subjects'         => $subjects,
    'enrollments'      => $enrollments,
    'matrix'           => $matrix,
    'summary'          => $summary,
    'statistics'       => $statistics,
    'attendanceCounts' => $attendanceCounts,
    'period'           => $period,
    'periodValue'      => $periodValue,
    'periodLabel'      => $periodLabel,
    'academicYear'     => $class->academicYear,
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

    // Calculate rank with TIES handled correctly (Standard Competition Ranking)
    // Example: 90, 90, 85 → ranks 1, 1, 3 (not 1, 2, 3)
    $ranked = collect($summary)
        ->filter(fn ($s) => $s['average'] !== null)
        ->sortByDesc('average')
        ->values();

    $currentRank    = 0;
    $previousAvg    = null;
    $sameRankCount  = 0;

    foreach ($ranked as $index => $item) {
        if ($previousAvg === null || $item['average'] < $previousAvg) {
            // New rank position
            $currentRank    = $index + 1;
            $sameRankCount  = 1;
        } else {
            // Same average as previous → same rank
            $sameRankCount++;
        }

        // Find the enrollment_id for this item
        $enrollmentId = collect($summary)->search(function ($s) use ($item) {
            return $s['average'] === $item['average'] 
                && $s['total'] === $item['total']
                && $s['rank'] === null;
        });

        if ($enrollmentId !== false) {
            $summary[$enrollmentId]['rank'] = $currentRank;
        }

        $previousAvg = $item['average'];
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
    /**
 * Get attendance counts per enrollment for the report period
 * Returns: [enrollment_id => ['absent' => X, 'late' => Y, 'excused' => Z]]
 */
private function getAttendanceCounts(SchoolClass $class, string $period, ?int $value, $enrollments): array
{
    $enrollmentIds = $enrollments->pluck('id');
    if ($enrollmentIds->isEmpty()) return [];

    // Determine which academic months to include
    $academicMonths = match($period) {
        'monthly'  => [$value],
        'semester' => \App\Helpers\AcademicCalendar::semesterMonths($value),
        'annual'   => range(1, 12),
        default    => [],
    };

    // Convert academic months to real calendar months
    // e.g., academic month 1 = October (calendar month 10)
    $calendarMonths = [];
    foreach ($academicMonths as $m) {
        $monthName = \App\Helpers\AcademicCalendar::monthName($m, 'en');
        if ($monthName) {
            $calendarMonths[] = (int) date('n', strtotime($monthName));
        }
    }

    if (empty($calendarMonths)) return [];

    // Query attendance counts per enrollment + status
    $records = \App\Models\Attendance::whereIn('enrollment_id', $enrollmentIds)
        ->whereHas('attendanceSession', function ($q) use ($calendarMonths, $class) {
            $q->whereIn(\DB::raw('MONTH(session_date)'), $calendarMonths)
              ->where('class_id', $class->id);
        })
        ->selectRaw('enrollment_id, status, COUNT(*) as total')
        ->groupBy('enrollment_id', 'status')
        ->get();

    // Build result map with defaults
    $result = [];
    foreach ($enrollments as $enrollment) {
        $result[$enrollment->id] = [
            'present' => 0,
            'absent'  => 0,
            'late'    => 0,
            'excused' => 0,
        ];
    }

    foreach ($records as $r) {
        $status = strtolower($r->status);
        if (isset($result[$r->enrollment_id][$status])) {
            $result[$r->enrollment_id][$status] = (int) $r->total;
        }
    }

    return $result;
}
}