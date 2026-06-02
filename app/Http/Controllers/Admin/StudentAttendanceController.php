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
    public function index(): View
    {
        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        return view('student-attendance.index', compact('classes'));
    }

    public function sheet(Request $request): View
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'month'    => ['required', 'integer', 'min:1', 'max:12'],
            'year'     => ['required', 'integer'],
        ]);

        $class  = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $month  = (int) $request->month;
        $year   = (int) $request->year;

        // Build all dates in this month
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $dates = collect(range(1, $daysInMonth))->map(fn($d) =>
            Carbon::create($year, $month, $d)
        );

        // Active enrollments with student phone
        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        // Load all attendance sessions for this class+month
        $sessions = AttendanceSession::where('class_id', $class->id)
            ->whereYear('session_date', $year)
            ->whereMonth('session_date', $month)
            ->pluck('id', 'session_date') // date => session_id
            ->mapWithKeys(fn($id, $date) => [
                Carbon::parse($date)->format('Y-m-d') => $id
            ]);

        // Load all attendance records for these sessions
        $attendanceMap = [];
        if ($sessions->isNotEmpty()) {
            $records = Attendance::whereIn('attendance_session_id', $sessions->values())
                ->whereIn('enrollment_id', $enrollments->pluck('id'))
                ->get();

            foreach ($records as $record) {
                // Find date for this session
                $date = $sessions->search($record->attendance_session_id);
                if ($date) {
                    $attendanceMap[$record->enrollment_id][$date] = $record->status;
                }
            }
        }

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')->orderBy('name')->get();

        return view('student-attendance.sheet', compact(
            'class', 'month', 'year', 'dates',
            'enrollments', 'attendanceMap', 'sessions', 'classes'
        ));
    }

    public function save(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'   => ['required', 'exists:classes,id'],
            'month'      => ['required', 'integer', 'min:1', 'max:12'],
            'year'       => ['required', 'integer'],
            'attendance' => ['required', 'array'],
        ]);

        $classId = $request->class_id;
        $month   = (int) $request->month;
        $year    = (int) $request->year;

        $validEnrollmentIds = Enrollment::where('class_id', $classId)
            ->where('status', 'active')
            ->pluck('id')
            ->flip();

        DB::transaction(function () use ($request, $classId, $month, $year, $validEnrollmentIds) {
            // attendance[YYYY-MM-DD][enrollment_id] = status
            foreach ($request->attendance as $date => $enrollmentStatuses) {
                // Validate date belongs to this month/year
                $carbonDate = Carbon::createFromFormat('Y-m-d', $date);
                if ($carbonDate->month !== $month || $carbonDate->year !== $year) continue;

                // Get or create the session for this date
                $session = AttendanceSession::firstOrCreate(
                    ['class_id' => $classId, 'session_date' => $date],
                    ['subject_id' => null, 'period' => null, 'topic' => null]
                );

                foreach ($enrollmentStatuses as $enrollmentId => $status) {
                    if (! isset($validEnrollmentIds[$enrollmentId])) continue;
                    if (! in_array($status, ['present', 'absent', 'late', 'excused'])) continue;

                    Attendance::updateOrCreate(
                        [
                            'enrollment_id'         => $enrollmentId,
                            'attendance_session_id' => $session->id,
                        ],
                        ['status' => $status]
                    );
                }
            }
        });

        return redirect()->route('admin.student-attendance.sheet', [
            'class_id' => $classId,
            'month'    => $month,
            'year'     => $year,
        ])->with('success', 'Attendance saved successfully.');
    }
}