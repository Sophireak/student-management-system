<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Admin\StudentAttendanceController as AdminController;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAttendanceController extends AdminController
{
    public function index(Request $request): View
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            abort(403, 'You are not registered as a teacher.');
        }

        $classId = $request->input('class_id');
        $date    = $request->input('date', now()->format('Y-m-d'));

        // ONLY teacher's classes
        $classes = $teacher->classes()
            ->with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')->orderBy('name')->get();

        // AUTO-SELECT if teacher has classes and no class_id in URL
        if (!$classId && $classes->isNotEmpty()) {
            $classId = $classes->first()->id;
        }

        // Empty state
        if (!$classId) {
            return view('teacher.student-attendance.index', [
                'classes'       => $classes,
                'class'         => null,
                'date'          => $date,
                'period'        => 'morning',
                'enrollments'   => collect(),
                'attendanceMap' => [],
                'total'         => 0,
                'presentCount'  => 0,
                'absentCount'   => 0,
                'lateCount'     => 0,
                'excusedCount'  => 0,
                'isLocked'      => false,
                'isSunday'      => false,
                'isPast'        => false,
                'isFuture'      => false,
                'khmerDate'     => $this->formatKhmerDateForTeacher(Carbon::parse($date)),
                'session'       => null,
            ]);
        }

        // Verify teacher owns this class
        $class = $classes->firstWhere('id', $classId);
        if (!$class) {
            abort(403, 'You are not assigned to this class.');
        }

        $period = $class->session_period ?? 'morning';

        $carbonDate = Carbon::parse($date);
        $today      = Carbon::today();
        $isSunday   = $carbonDate->dayOfWeek === Carbon::SUNDAY;
        $isPast     = $carbonDate->lt($today);
        $isFuture   = $carbonDate->gt($today);

        // Block future
        if ($isFuture) {
            return view('teacher.student-attendance.index', [
                'classes'       => $classes,
                'class'         => $class,
                'date'          => $date,
                'period'        => $period,
                'enrollments'   => collect(),
                'attendanceMap' => [],
                'total'         => 0,
                'presentCount'  => 0,
                'absentCount'   => 0,
                'lateCount'     => 0,
                'excusedCount'  => 0,
                'isLocked'      => true,
                'isSunday'      => false,
                'isPast'        => false,
                'isFuture'      => true,
                'khmerDate'     => $this->formatKhmerDateForTeacher($carbonDate),
                'session'       => null,
            ]);
        }

        // Get active enrollments
        $enrollments = Enrollment::with('student')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->get()
            ->sortBy(fn($e) => $e->student->last_name . ' ' . $e->student->first_name)
            ->values();

        $session = null;
        $attendanceMap = [];
        $isLocked = false;

        if (!$isSunday && $enrollments->isNotEmpty()) {
            $session = AttendanceSession::firstOrCreate(
                ['class_id' => $classId, 'session_date' => $date, 'period' => $period],
                ['subject_id' => null, 'topic' => null]
            );

            // Auto-lock past
            if ($isPast && !$session->isLocked()) {
                $session->autoLock();
            }

            $isLocked = !$session->canEdit(auth()->user());

            $records = Attendance::where('attendance_session_id', $session->id)
                ->get()
                ->keyBy('enrollment_id');

            // Build map (auto-present visually, but no DB write)
            foreach ($enrollments as $enrollment) {
                $record = $records->get($enrollment->id);
                $attendanceMap[$enrollment->id] = [
                    'status' => $record?->status ?? 'present',
                    'notes'  => $record?->notes ?? '',
                ];
            }
        }

        return view('teacher.student-attendance.index', [
            'classes'       => $classes,
            'class'         => $class,
            'date'          => $date,
            'period'        => $period,
            'enrollments'   => $enrollments,
            'attendanceMap' => $attendanceMap,
            'total'         => $enrollments->count(),
            'presentCount'  => collect($attendanceMap)->where('status', 'present')->count(),
            'absentCount'   => collect($attendanceMap)->where('status', 'absent')->count(),
            'lateCount'     => collect($attendanceMap)->where('status', 'late')->count(),
            'excusedCount'  => collect($attendanceMap)->where('status', 'excused')->count(),
            'isLocked'      => $isLocked,
            'isSunday'      => $isSunday,
            'isPast'        => $isPast,
            'isFuture'      => $isFuture,
            'khmerDate'     => $this->formatKhmerDateForTeacher($carbonDate),
            'session'       => $session,
        ]);
    }

    /**
     * Format date in Khmer
     */
    private function formatKhmerDateForTeacher(Carbon $date): string
    {
        $days = [
            'Sunday'    => 'ថ្ងៃអាទិត្យ',
            'Monday'    => 'ថ្ងៃច័ន្ទ',
            'Tuesday'   => 'ថ្ងៃអង្គារ',
            'Wednesday' => 'ថ្ងៃពុធ',
            'Thursday'  => 'ថ្ងៃព្រហស្បតិ៍',
            'Friday'    => 'ថ្ងៃសុក្រ',
            'Saturday'  => 'ថ្ងៃសៅរ៍',
        ];

        $months = [
            1  => 'មករា',   2  => 'កុម្ភ',    3  => 'មីនា',
            4  => 'មេសា',   5  => 'ឧសភា',    6  => 'មិថុនា',
            7  => 'កក្កដា', 8  => 'សីហា',    9  => 'កញ្ញា',
            10 => 'តុលា',   11 => 'វិច្ឆិកា', 12 => 'ធ្នូ',
        ];

        $khmerNumbers = ['០','១','២','៣','៤','៥','៦','៧','៨','៩'];
        $day  = $date->format('j');
        $year = $date->format('Y');

        $khmerDay  = strtr($day,  array_combine(range('0','9'), $khmerNumbers));
        $khmerYear = strtr($year, array_combine(range('0','9'), $khmerNumbers));

        $dayName   = $days[$date->format('l')] ?? '';
        $monthName = $months[$date->format('n')] ?? '';

        return "{$dayName} ទី{$khmerDay} ខែ{$monthName} ឆ្នាំ{$khmerYear}";
    }
}