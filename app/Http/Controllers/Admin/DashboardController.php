<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalStudents'    => Student::count(),
            'totalTeachers'    => Teacher::count(),
            'totalClasses'     => SchoolClass::count(),
            'totalEnrollments' => Enrollment::count(),
            'activeYear'       => AcademicYear::where('is_active', true)->first(),
        ]);
    }
}
