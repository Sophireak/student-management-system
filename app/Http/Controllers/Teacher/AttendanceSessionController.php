<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceSessionRequest;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttendanceSessionController extends Controller
{
    private function getTeacher()
    {
        return auth()->user()->teacher;
    }

    public function index(): View
    {
        $teacher = $this->getTeacher();

        // Only sessions for this teacher's classes
        $classIds = $teacher->classes()->pluck('classes.id');

        $sessions = AttendanceSession::with([
                        'schoolClass.grade',
                        'subject',
                    ])
                    ->withCount('attendances')
                    ->whereIn('class_id', $classIds)
                    ->latest('session_date')
                    ->paginate(20);

        return view('teacher.attendance-sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $teacher = $this->getTeacher();

        // Teacher sees only their assigned classes
        $classes = $teacher->classes()
                           ->with(['grade', 'academicYear'])
                           ->whereHas('academicYear', fn($q) =>
                               $q->where('is_active', true)
                           )
                           ->get();

        // Subjects are filtered by grade in the view via JS or shown all
        $subjects = Subject::with('grade')
                           ->orderBy('grade_id')
                           ->get();

        return view('teacher.attendance-sessions.create',
                    compact('classes', 'subjects'));
    }

    public function store(StoreAttendanceSessionRequest $request): RedirectResponse
    {
        $teacher = $this->getTeacher();

        // Verify teacher is assigned to the selected class
        $isAssigned = $teacher->classes()
                              ->where('classes.id', $request->class_id)
                              ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not assigned to this class.');
        }

        $activeCount = Enrollment::where('class_id', $request->class_id)
                                 ->where('status', 'active')
                                 ->count();

        if ($activeCount === 0) {
            return back()
                ->withInput()
                ->with('error', 'This class has no active enrollments.');
        }

        $session = AttendanceSession::create($request->validated());

        return redirect()
            ->route('teacher.attendance-sessions.show', $session)
            ->with('success', 'Session created. Now mark attendance.');
    }

    public function show(AttendanceSession $attendanceSession): View
    {
        $teacher = $this->getTeacher();

        // Verify ownership
        $isAssigned = $teacher->classes()
                              ->where('classes.id', $attendanceSession->class_id)
                              ->exists();

        if (! $isAssigned) {
            abort(403);
        }

        $attendanceSession->load([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
            'attendances.enrollment.student',
        ]);

        // Load active enrollments not yet marked
        $markedEnrollmentIds = $attendanceSession
            ->attendances()
            ->pluck('enrollment_id');

        $unmarkedEnrollments = Enrollment::with('student')
            ->where('class_id', $attendanceSession->class_id)
            ->where('status', 'active')
            ->whereNotIn('id', $markedEnrollmentIds)
            ->orderBy('id')
            ->get();

        $isFullyMarked = $unmarkedEnrollments->isEmpty();

        return view('teacher.attendance-sessions.show', compact(
            'attendanceSession',
            'unmarkedEnrollments',
            'isFullyMarked'
        ));
    }

    public function destroy(AttendanceSession $attendanceSession): RedirectResponse
    {
        $teacher = $this->getTeacher();

        $isAssigned = $teacher->classes()
                              ->where('classes.id', $attendanceSession->class_id)
                              ->exists();

        if (! $isAssigned) {
            abort(403);
        }

        $attendanceSession->delete();

        return redirect()
            ->route('teacher.attendance-sessions.index')
            ->with('success', 'Session deleted.');
    }
}