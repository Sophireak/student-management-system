<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\View\View;

class ScoreController extends Controller
{
    // Admin view only — score entry done by teachers
    public function index(ExamSession $examSession): View
    {
        $examSession->load([
            'schoolClass.grade',
            'schoolClass.academicYear',
            'subject',
            'scores.enrollment.student',
        ]);

        return view('admin.exam-sessions.scores', compact('examSession'));
    }
}