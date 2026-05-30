<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceSessionRequest;
use App\Models\AttendanceSession;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    public function index(): View
    {
        $sessions = AttendanceSession::with([
                        'schoolClass.grade',
                        'schoolClass.academicYear',
                        'subject',
                    ])
                    ->withCount('attendances')
                    ->latest('session_date')
                    ->paginate(20);

        return view('admin.attendance-sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $classes  = SchoolClass::with(['grade', 'academicYear'])
                               ->whereHas('academicYear', fn($q) =>
                                   $q->where('is_active', true)
                               )
                               ->orderBy('grade_id')
                               ->orderBy('name')
                               ->get();

        $subjects = Subject::with('grade')->orderBy('grade_id')->get();

        return view('admin.attendance-sessions.create',
                    compact('classes', 'subjects'));
    }

    public function store(StoreAttendanceSessionRequest $request): RedirectResponse
    {
        // Verify class has active enrollments before creating session
        $activeCount = \App\Models\Enrollment::where('class_id', $request->class_id)
                                             ->where('status', 'active')
                                             ->count();

        if ($activeCount === 0) {
            return back()
                ->withInput()
                ->with('error', 'Cannot create a session for a class with no active enrollments.');
        }

        $session = AttendanceSession::create($request->validated());

        return redirect()
            ->route('admin.attendance-sessions.show', $session)
            ->with('success', 'Attendance session created.');
    }

    public function show(AttendanceSession $attendanceSession): View
    {
        $attendanceSession->load([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
            'attendances.enrollment.student',
        ]);

        return view('admin.attendance-sessions.show',
                    compact('attendanceSession'));
    }

    public function destroy(AttendanceSession $attendanceSession): RedirectResponse
    {
        // Cascade handles attendance rows automatically
        $attendanceSession->delete();

        return redirect()
            ->route('admin.attendance-sessions.index')
            ->with('success', 'Attendance session deleted.');
    }
}