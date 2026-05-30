<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    // Admin view only — editing done by teachers
    public function index(AttendanceSession $attendanceSession): View
    {
        $attendanceSession->load([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
            'attendances.enrollment.student',
        ]);

        return view('admin.attendance-sessions.attendance',
                    compact('attendanceSession'));
    }
}