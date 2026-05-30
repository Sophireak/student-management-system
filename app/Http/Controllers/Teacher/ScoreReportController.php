<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\MonthlyScore;
use App\Models\SemesterScore;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoreReportController extends Controller
{
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

    public function index(): View
    {
        $teacher = $this->getTeacher();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->take(3)->get();

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('classTeachers', fn($q) => $q->where('teacher_id', $teacher->id))
            ->whereIn('academic_year_id', $academicYears->pluck('id'))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        return view('score-report.index', compact('academicYears', 'classes'));
    }

    public function show(Request $request): View
    {
        $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id'         => ['required', 'exists:classes,id'],
            'period'           => ['required', 'string'],
        ]);

        $this->authorizeClass($request->class_id);

        [$type, $value] = explode('_', $request->period, 2);
        $value = (int) $value;

        $teacher      = $this->getTeacher();
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $class        = SchoolClass::with('grade')->findOrFail($request->class_id);

        $enrollments = Enrollment::with('student')
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->get();

        $rows = $this->buildRows($enrollments, $request->academic_year_id, $type, $value);

        $periodLabel = $this->periodLabel($type, $value);

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->take(3)->get();
        $classes       = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('classTeachers', fn($q) => $q->where('teacher_id', $teacher->id))
            ->whereIn('academic_year_id', $academicYears->pluck('id'))
            ->orderBy('grade_id')->orderBy('name')->get();

        return view('score-report.index', compact(
            'academicYears', 'classes', 'class', 'academicYear',
            'rows', 'periodLabel', 'type', 'value'
        ));
    }

    private function buildRows($enrollments, int $yearId, string $type, int $value): array
    {
        $rows = [];

        foreach ($enrollments as $enrollment) {
            if ($type === 'month') {
                $scores = MonthlyScore::where('enrollment_id', $enrollment->id)
                    ->where('academic_year_id', $yearId)
                    ->where('month', $value)
                    ->get();
            } else {
                $scores = SemesterScore::where('enrollment_id', $enrollment->id)
                    ->where('academic_year_id', $yearId)
                    ->where('semester', $value)
                    ->get();
            }

            $numericScores = $scores->whereNotNull('score')->pluck('score');
            $total         = $numericScores->sum();
            $average       = $numericScores->count() > 0
                ? round($total / $numericScores->count(), 2)
                : null;

            $passed  = $average !== null ? $average >= 50 : null;
            $remark  = $passed === null ? '—' : ($passed ? 'Pass' : 'Fail');
            $grade   = $this->gradeFromAverage($average);

            $rows[] = [
                'enrollment' => $enrollment,
                'student'    => $enrollment->student,
                'total'      => $average !== null ? number_format($total, 2) : '—',
                'average'    => $average !== null ? number_format($average, 2) : '—',
                'grade'      => $grade,
                'remark'     => $remark,
                'passed'     => $passed,
            ];
        }

        return $rows;
    }

    private function gradeFromAverage(?float $avg): string
    {
        if ($avg === null) return '—';
        if ($avg >= 80)   return 'A';
        if ($avg >= 70)   return 'B';
        if ($avg >= 60)   return 'C';
        if ($avg >= 50)   return 'D';
        return 'F';
    }

    private function periodLabel(string $type, int $value): string
    {
        $months = [
            1=>'September', 2=>'October',  3=>'November', 4=>'December',
            5=>'January',   6=>'February', 7=>'March',    8=>'April', 9=>'May',
        ];

        if ($type === 'month') {
            return 'Month ' . $value . ' — ' . ($months[$value] ?? '');
        }

        return 'Semester ' . $value . ($value === 1 ? ' (Sep – Jan)' : ' (Feb – May)');
    }
}