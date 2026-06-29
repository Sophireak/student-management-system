<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentAttendanceController extends Controller
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

    private function getTeacherClasses()
    {
        $teacher = $this->getTeacher();
        return SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('classTeachers', fn($q) => $q->where('teacher_id', $teacher->id))
            ->whereHas('academicYear',  fn($q) => $q->where('is_active', true))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();
    }

    public function index(Request $request): View
    {
        $classes = $this->getTeacherClasses();

        if (! $request->filled('class_id')) {
            return view('teacher.attendance-sessions.index', compact('classes'));
        }

        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'month'    => ['required', 'integer', 'min:1', 'max:12'],
            'year'     => ['required', 'integer'],
        ]);

        $this->authorizeClass($request->class_id);

        $class = SchoolClass::with('grade', 'academicYear')->findOrFail($request->class_id);
        $month = (int) $request->month;
        $year  = (int) $request->year;

        $dates = collect(range(1, Carbon::create($year, $month)->daysInMonth))
            ->map(fn($d) => Carbon::create($year, $month, $d));

        $enrollments = Enrollment::with('student')
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $sessions = AttendanceSession::where('class_id', $class->id)
            ->whereYear('session_date', $year)
            ->whereMonth('session_date', $month)
            ->pluck('id', 'session_date')
            ->mapWithKeys(fn($id, $date) => [
                Carbon::parse($date)->format('Y-m-d') => $id
            ]);

        $attendanceMap = [];
        if ($sessions->isNotEmpty()) {
            $records = Attendance::whereIn('attendance_session_id', $sessions->values())
                ->whereIn('enrollment_id', $enrollments->pluck('id'))
                ->get();

            foreach ($records as $record) {
                $date = $sessions->search($record->attendance_session_id);
                if ($date) {
                    $attendanceMap[$record->enrollment_id][$date] = $record->status;
                }
            }
        }

        return view('teacher.attendance-sessions.index', compact(
            'classes', 'class', 'month', 'year',
            'dates', 'enrollments', 'attendanceMap', 'sessions'
        ));
    }

    public function sheet(Request $request): RedirectResponse
    {
        return redirect()->route('teacher.student-attendance.index', $request->only([
            'class_id', 'month', 'year'
        ]));
    }

    public function saveSingle(Request $request): JsonResponse
    {
        $request->validate([
            'class_id'      => ['required', 'exists:classes,id'],
            'date'          => ['required', 'date_format:Y-m-d'],
            'enrollment_id' => ['required', 'exists:enrollments,id'],
            'status'        => ['nullable', 'in:present,absent,late,excused'],
            'note'          => ['nullable', 'string', 'max:255'],
        ]);

        $this->authorizeClass($request->class_id);

        $enrollment = Enrollment::where('id', $request->enrollment_id)
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->firstOrFail();

        $session = AttendanceSession::firstOrCreate(
            ['class_id' => $request->class_id, 'session_date' => $request->date],
            ['subject_id' => null, 'period' => null, 'topic' => null]
        );

        if (empty($request->status)) {
            Attendance::where([
                'enrollment_id'         => $enrollment->id,
                'attendance_session_id' => $session->id,
            ])->delete();
        } else {
            Attendance::updateOrCreate(
                [
                    'enrollment_id'         => $enrollment->id,
                    'attendance_session_id' => $session->id,
                ],
                [
                    'status' => $request->status,
                    'notes'  => $request->note,
                ]
            );
        }

        return response()->json(['success' => true]);
    }
}