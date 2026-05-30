<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    private function authorizeSession(AttendanceSession $session): void
    {
        $isAssigned = $this->getTeacher()
                           ->classes()
                           ->where('classes.id', $session->class_id)
                           ->exists();

        if (! $isAssigned) {
            abort(403);
        }
    }

    public function index(AttendanceSession $attendanceSession): View
    {
        $this->authorizeSession($attendanceSession);

        $attendanceSession->load([
            'schoolClass.grade',
            'subject',
            'attendances.enrollment.student',
        ]);

        return view('teacher.attendance-sessions.attendance',
                    compact('attendanceSession'));
    }

    public function store(
        StoreAttendanceRequest $request,
        AttendanceSession $attendanceSession
    ): RedirectResponse {
        $this->authorizeSession($attendanceSession);

        $records   = $request->validated()['attendance'];
        $inserted  = 0;
        $skipped   = 0;

        foreach ($records as $record) {
            // Skip if already marked — prevents duplicates on double-submit
            $exists = Attendance::where('enrollment_id',          $record['enrollment_id'])
                                ->where('attendance_session_id',  $attendanceSession->id)
                                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Verify enrollment belongs to this session's class
            $validEnrollment = Enrollment::where('id',       $record['enrollment_id'])
                                         ->where('class_id', $attendanceSession->class_id)
                                         ->where('status',   'active')
                                         ->exists();

            if (! $validEnrollment) {
                continue;
            }

            Attendance::create([
                'enrollment_id'         => $record['enrollment_id'],
                'attendance_session_id' => $attendanceSession->id,
                'status'                => $record['status'],
                'notes'                 => $record['notes'] ?? null,
            ]);

            $inserted++;
        }

        $message = "{$inserted} attendance record(s) saved.";
        if ($skipped > 0) {
            $message .= " {$skipped} already marked record(s) skipped.";
        }

        return redirect()
            ->route('teacher.attendance-sessions.show', $attendanceSession)
            ->with('success', $message);
    }

    public function update(
        UpdateAttendanceRequest $request,
        AttendanceSession $attendanceSession,
        Attendance $attendance
    ): RedirectResponse {
        $this->authorizeSession($attendanceSession);

        // Verify the attendance belongs to this session
        if ($attendance->attendance_session_id !== $attendanceSession->id) {
            abort(403);
        }

        $attendance->update($request->validated());

        return redirect()
            ->route('teacher.attendance-sessions.show', $attendanceSession)
            ->with('success', 'Attendance record updated.');
    }
}