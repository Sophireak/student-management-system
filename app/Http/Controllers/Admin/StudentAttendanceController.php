<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
{
public function index(Request $request): View
{
    $classId = $request->input('class_id');
    $date    = $request->input('date', now()->format('Y-m-d'));

    // All active classes for dropdown
    $classes = SchoolClass::with(['grade', 'academicYear'])
        ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
        ->orderBy('grade_id')->orderBy('name')->get();

    // Empty state
    if (!$classId) {
        return view('admin.student-attendance.index', [
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
            'khmerDate'     => $this->formatKhmerDate(Carbon::parse($date)),
            'session'       => null,
        ]);
    }

    $class = SchoolClass::with(['grade', 'academicYear'])->findOrFail($classId);
    $period = $class->session_period ?? 'morning'; // Use class's session

    $carbonDate = Carbon::parse($date);
    $today      = Carbon::today();
    $isSunday   = $carbonDate->dayOfWeek === Carbon::SUNDAY;
    $isPast     = $carbonDate->lt($today);
    $isFuture   = $carbonDate->gt($today);

    // Block future dates
    if ($isFuture) {
        return view('admin.student-attendance.index', [
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
            'khmerDate'     => $this->formatKhmerDate($carbonDate),
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

        // Auto-lock past dates (for teachers only)
        if ($isPast && !$session->isLocked() && !auth()->user()->isAdmin()) {
            $session->autoLock();
        }

        // Check lock
        $isLocked = !$session->canEdit(auth()->user());

        // Get existing attendance
        $records = Attendance::where('attendance_session_id', $session->id)
            ->get()
            ->keyBy('enrollment_id');

// Build map (auto-present visually, but no DB write)
foreach ($enrollments as $enrollment) {
    $record = $records->get($enrollment->id);
    $attendanceMap[$enrollment->id] = [
        'status' => $record?->status ?? 'present', // Default visual = present
        'notes'  => $record?->notes ?? '',
    ];
}

        // Build map
        foreach ($enrollments as $enrollment) {
            $record = $records->get($enrollment->id);
            $attendanceMap[$enrollment->id] = [
                'status' => $record?->status ?? 'present',
                'notes'  => $record?->notes ?? '',
            ];
        }
    }

    return view('admin.student-attendance.index', [
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
        'khmerDate'     => $this->formatKhmerDate($carbonDate),
        'session'       => $session,
    ]);
}

/**
 * Detect current period based on time
 */
private function detectPeriod(): string
{
    $now = now();
    $hour = (int) $now->format('H');

    if ($hour < 12) {
        return 'morning';
    }

    return 'afternoon';
}

public function save(Request $request)
{
    $request->validate([
        'class_id'    => ['required', 'exists:classes,id'],
        'date'        => ['required', 'date'],
        'period'      => ['required', 'in:morning,afternoon'],
        'attendance'  => ['required', 'array'],
        'attendance.*.enrollment_id' => ['required', 'exists:enrollments,id'],
        'attendance.*.status'        => ['required', 'in:present,absent,late,excused'],
        'attendance.*.notes'         => ['nullable', 'string', 'max:500'],
    ]);

    $classId = $request->class_id;
    $date    = $request->date;
    $period  = $request->period;

    // Get or create session
    $session = AttendanceSession::firstOrCreate(
        ['class_id' => $classId, 'session_date' => $date, 'period' => $period],
        ['subject_id' => null, 'topic' => null]
    );

    // Check lock
    if (!$session->canEdit(auth()->user())) {
        return response()->json([
            'success' => false,
            'message' => 'Session is locked and cannot be edited.'
        ], 403);
    }

    // Valid enrollment IDs for this class
    $validEnrollmentIds = Enrollment::where('class_id', $classId)
        ->where('status', 'active')
        ->pluck('id')
        ->flip();

    DB::transaction(function () use ($request, $session, $validEnrollmentIds) {
        foreach ($request->attendance as $item) {
            $enrollmentId = $item['enrollment_id'];
            
            if (!isset($validEnrollmentIds[$enrollmentId])) continue;

            Attendance::updateOrCreate(
                [
                    'enrollment_id'         => $enrollmentId,
                    'attendance_session_id' => $session->id,
                ],
                [
                    'status' => $item['status'],
                    'notes'  => $item['notes'] ?? null,
                ]
            );
        }
    });

    return response()->json([
        'success' => true,
        'message' => 'Attendance saved successfully.',
        'saved_count' => count($request->attendance),
    ]);
}
/**
 * Format date in Khmer
 */
private function formatKhmerDate(\Carbon\Carbon $date): string
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