<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user()->teacher;

        if (! $teacher) {
            return view('teacher.dashboard', [
                'classes'        => collect(),
                'recentSessions' => collect(),
            ]);
        }

        $classes = $teacher->classes()
            ->with(['grade', 'academicYear'])
            ->whereHas(
                'academicYear',
                fn($q) =>
                $q->where('is_active', true)
            )
            ->withCount([
                'enrollments as active_students' => fn($q) =>
                $q->where('status', 'active'),
            ])
            ->get();

        $recentSessions = AttendanceSession::whereIn(
            'class_id',
            $classes->pluck('id')
        )
            ->with(['schoolClass', 'subject'])
            ->latest('session_date')
            ->limit(5)
            ->get();

        return view('teacher.dashboard', compact('classes', 'recentSessions'));
    }
}
