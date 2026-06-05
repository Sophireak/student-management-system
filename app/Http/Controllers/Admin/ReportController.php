<?php

namespace App\Http\Controllers\Admin;

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
    private function filterData(): array
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $classes       = SchoolClass::with(['grade', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();
        return compact('academicYears', 'classes');
    }

    public function rankingIndex(): View
    {
        return view('reports.ranking.index', $this->filterData());
    }

    public function rankingSheet(Request $request): View
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'period'           => ['required', 'string'],
        ]);

        [$type, $value] = explode('_', $request->period, 2);

        $class        = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $subjects     = Subject::where('grade_id', $class->grade_id)->orderBy('name')->get();

        $enrollments   = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();
        $enrollmentIds = $enrollments->pluck('id');

        if ($type === 'month') {
            $scores      = MonthlyScore::whereIn('enrollment_id', $enrollmentIds)
                ->where('academic_year_id', $academicYear->id)
                ->where('month', (int) $value)
                ->get()
                ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");
            $periodLabel = 'Month ' . $value . ' — ' . \App\Models\MonthlyScore::monthName((int) $value);
        } else {
            $scores      = SemesterScore::whereIn('enrollment_id', $enrollmentIds)
                ->where('academic_year_id', $academicYear->id)
                ->where('semester', (int) $value)
                ->get()
                ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");
            $periodLabel = \App\Models\SemesterScore::semesterLabel((int) $value);
        }

        // Build rows as plain array
        $rawRows = [];
        foreach ($enrollments as $enrollment) {
            $numericTotal  = 0;
            $numericCount  = 0;
            $subjectScores = [];

            foreach ($subjects as $subject) {
                $key   = "{$enrollment->id}:{$subject->id}";
                $score = $scores->get($key);

                if ($subject->isNumeric() && $score?->score !== null) {
                    $numericTotal += $score->score;
                    $numericCount++;
                    $subjectScores[$subject->id] = $score->score;
                } elseif ($subject->isGrade()) {
                    $subjectScores[$subject->id] = $score?->grade ?? '—';
                } else {
                    $subjectScores[$subject->id] = $score?->pass_fail ?? '—';
                }
            }

            if ($type === 'semester') {
                $anyScore = $scores->first(fn($s) => $s->enrollment_id === $enrollment->id);
                $average  = $anyScore?->average_score ?? ($numericCount > 0 ? round($numericTotal / $numericCount, 2) : null);
                $total    = $anyScore?->total_score   ?? ($numericCount > 0 ? round($numericTotal, 2) : null);
            } else {
                $average = $numericCount > 0 ? round($numericTotal / $numericCount, 2) : null;
                $total   = $numericCount > 0 ? round($numericTotal, 2) : null;
            }

            $rawRows[] = [
                'enrollment'     => $enrollment,
                'subject_scores' => $subjectScores,
                'total'          => $total,
                'average'        => $average,
                'letter_grade'   => $this->letterGrade($average),
                'rank'           => null,
            ];
        }

        // Sort by average descending
        usort($rawRows, fn($a, $b) => ($b['average'] ?? -1) <=> ($a['average'] ?? -1));

        // Assign dense rank
        $rank    = 1;
        $prevAvg = null;
        foreach ($rawRows as $i => &$row) {
            if ($prevAvg !== null && $row['average'] < $prevAvg) {
                $rank = $i + 1;
            }
            $row['rank'] = $row['average'] !== null ? $rank : '—';
            $prevAvg = $row['average'];
        }
        unset($row);

        $rows           = collect($rawRows);
        $selectedPeriod = $request->period;

        return view('reports.ranking.sheet', array_merge(
            $this->filterData(),
            compact('class', 'academicYear', 'subjects', 'rows', 'periodLabel', 'selectedPeriod')
        ));
    }

    public function honorsIndex(): View
    {
        return view('reports.honors.index', $this->filterData());
    }

    public function honorsSheet(Request $request): View
    {
        $request->validate([
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'period'           => ['required', 'string'],
            'top'              => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        [$type, $value] = explode('_', $request->period, 2);

        $class        = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $topN         = (int) ($request->top ?? 5);

        $enrollments   = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();
        $enrollmentIds = $enrollments->pluck('id');

        if ($type === 'month') {
            $scores      = MonthlyScore::whereIn('enrollment_id', $enrollmentIds)
                ->where('academic_year_id', $academicYear->id)
                ->where('month', (int) $value)
                ->get()
                ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");
            $periodLabel = 'Month ' . $value . ' — ' . \App\Models\MonthlyScore::monthName((int) $value);
        } else {
            $scores      = SemesterScore::whereIn('enrollment_id', $enrollmentIds)
                ->where('academic_year_id', $academicYear->id)
                ->where('semester', (int) $value)
                ->get()
                ->keyBy(fn($s) => "{$s->enrollment_id}:{$s->subject_id}");
            $periodLabel = \App\Models\SemesterScore::semesterLabel((int) $value);
        }

        $rawRows = [];
        foreach ($enrollments as $enrollment) {
            $numericTotal = 0;
            $numericCount = 0;

            foreach ($subjects ?? Subject::where('grade_id', $class->grade_id)->get() as $subject) {
                $key   = "{$enrollment->id}:{$subject->id}";
                $score = $scores->get($key);
                if ($subject->isNumeric() && $score?->score !== null) {
                    $numericTotal += $score->score;
                    $numericCount++;
                }
            }

            if ($type === 'semester') {
                $anyScore = $scores->first(fn($s) => $s->enrollment_id === $enrollment->id);
                $average  = $anyScore?->average_score ?? ($numericCount > 0 ? round($numericTotal / $numericCount, 2) : null);
                $total    = $anyScore?->total_score   ?? ($numericCount > 0 ? round($numericTotal, 2) : null);
            } else {
                $average = $numericCount > 0 ? round($numericTotal / $numericCount, 2) : null;
                $total   = $numericCount > 0 ? round($numericTotal, 2) : null;
            }

            $rawRows[] = [
                'enrollment'   => $enrollment,
                'total'        => $total,
                'average'      => $average,
                'letter_grade' => $this->letterGrade($average),
                'rank'         => null,
            ];
        }

        usort($rawRows, fn($a, $b) => ($b['average'] ?? -1) <=> ($a['average'] ?? -1));

        $rawRows = array_slice($rawRows, 0, $topN);

        $rank    = 1;
        $prevAvg = null;
        foreach ($rawRows as $i => &$row) {
            if ($prevAvg !== null && $row['average'] < $prevAvg) {
                $rank = $i + 1;
            }
            $row['rank'] = $row['average'] !== null ? $rank : '—';
            $prevAvg = $row['average'];
        }
        unset($row);

        $rows           = collect($rawRows);
        $selectedPeriod = $request->period;

        return view('reports.honors.sheet', array_merge(
            $this->filterData(),
            compact('class', 'academicYear', 'rows', 'periodLabel', 'topN', 'selectedPeriod')
        ));
    }

    private function letterGrade(?float $average): string
    {
        if ($average === null) return '—';
        return match(true) {
            $average >= 80 => 'A',
            $average >= 70 => 'B',
            $average >= 60 => 'C',
            $average >= 50 => 'D',
            default        => 'E',
        };
    }
}