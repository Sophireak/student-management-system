<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\ExamSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Basic counts
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalClasses = SchoolClass::count();
        $totalSubjects = \App\Models\Subject::count();
        $totalEnrollments = Enrollment::where('status', 'active')->count();

        // Today's attendance rate
        $todaySessions = AttendanceSession::whereDate('session_date', $today)->pluck('id');
        $todayAttendance = 0;
        $todayTotal = 0;

        if ($todaySessions->isNotEmpty()) {
            $todayTotal = Attendance::whereIn('attendance_session_id', $todaySessions)->count();
            $todayPresent = Attendance::whereIn('attendance_session_id', $todaySessions)
                ->whereIn('status', ['present', 'late'])
                ->count();
            $todayAttendance = $todayTotal > 0 
                ? round(($todayPresent / $todayTotal) * 100) 
                : 0;
        }

        // This month's attendance rate
        $monthSessions = AttendanceSession::whereMonth('session_date', $thisMonth)
            ->whereYear('session_date', $thisYear)
            ->pluck('id');
        $monthAttendance = 0;

        if ($monthSessions->isNotEmpty()) {
            $monthTotal = Attendance::whereIn('attendance_session_id', $monthSessions)->count();
            $monthPresent = Attendance::whereIn('attendance_session_id', $monthSessions)
                ->whereIn('status', ['present', 'late'])
                ->count();
            $monthAttendance = $monthTotal > 0 
                ? round(($monthPresent / $monthTotal) * 100) 
                : 0;
        }

        // Sessions today
        $sessionsToday = AttendanceSession::whereDate('session_date', $today)->count();

        // Recent attendance sessions
        $recentSessions = AttendanceSession::with(['schoolClass', 'subject'])
            ->latest('session_date')
            ->limit(5)
            ->get();

        // Recent exam sessions
        $recentExams = ExamSession::with(['schoolClass', 'subject'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // New students this month
        $newStudentsThisMonth = Student::whereMonth('created_at', $thisMonth)
            ->whereYear('created_at', $thisYear)
            ->count();

        // New enrollments this month
        $newEnrollmentsThisMonth = Enrollment::whereMonth('created_at', $thisMonth)
            ->whereYear('created_at', $thisYear)
            ->count();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalSubjects',
            'totalEnrollments',
            'activeYear',
            'todayAttendance',
            'todayTotal',
            'monthAttendance',
            'sessionsToday',
            'recentSessions',
            'recentExams',
            'newStudentsThisMonth',
            'newEnrollmentsThisMonth',
        ));
    }
}