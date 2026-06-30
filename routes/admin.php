<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ClassTeacherController;
use App\Http\Controllers\Teacher\StudentAttendanceController;
use App\Http\Controllers\Teacher\ExaminationScoreController;
use App\Http\Controllers\Teacher\MonthlyReportController;
use App\Http\Controllers\Teacher\SemesterReportController;
use App\Http\Controllers\Teacher\AnnualReportController;
use App\Http\Controllers\Teacher\ReportController;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Dashboard ──────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Academic Years ─────────────────────────────────────────
        Route::resource('academic-years', AcademicYearController::class)->except(['show']);
        Route::post('academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');
        Route::post('academic-years/{academicYear}/toggle-active', [AcademicYearController::class, 'toggleActive'])->name('academic-years.toggle-active');

        // ── Grades ──────────────────────────────────────────────────
        Route::resource('grades', GradeController::class);

        // ── Subjects ────────────────────────────────────────────────
        Route::resource('subjects', SubjectController::class);

        // ── Classes ─────────────────────────────────────────────────
        Route::resource('classes', SchoolClassController::class);
        Route::post('classes/{schoolClass}/toggle-active', [SchoolClassController::class, 'toggleActive'])->name('classes.toggle-active');

        // ── Teachers ────────────────────────────────────────────────
        Route::resource('teachers', TeacherController::class);

        // ── Students ────────────────────────────────────────────────
        Route::resource('students', StudentController::class);

        // ── Enrollments ─────────────────────────────────────────────
        Route::resource('enrollments', EnrollmentController::class);
        Route::post('enrollments/{enrollment}/toggle-status', [EnrollmentController::class, 'toggleStatus'])->name('enrollments.toggle-status');

        // ── Teacher Assignments ─────────────────────────────────────
        Route::resource('class-teachers', ClassTeacherController::class);

        // ── Student Attendance (Admin) ────────────────────────────
        Route::prefix('student-attendance')->name('student-attendance.')->group(function () {
            Route::get('/',            [\App\Http\Controllers\Admin\StudentAttendanceController::class, 'index'])->name('index');
            Route::get('/sheet',       [\App\Http\Controllers\Admin\StudentAttendanceController::class, 'sheet'])->name('sheet');
            Route::post('/save-single', [\App\Http\Controllers\Admin\StudentAttendanceController::class, 'saveSingle'])->name('save-single');
        });

        // ── Examination Scores (Admin view) ──────────────────────
        Route::prefix('examination-scores')->name('examination-scores.')->group(function () {
            Route::get('/',               [ExaminationScoreController::class, 'index'])->name('index');
            Route::get('/sheet',          [ExaminationScoreController::class, 'sheet'])->name('sheet');
            Route::post('/save-monthly',  [ExaminationScoreController::class, 'saveMonthly'])->name('save-monthly');
            Route::post('/save-semester', [ExaminationScoreController::class, 'saveSemester'])->name('save-semester');
        });

        // ── Monthly Report ─────────────────────────────────────────
        Route::prefix('monthly-report')->name('monthly-report.')->group(function () {
            Route::get('/',      [MonthlyReportController::class, 'index'])->name('index');
            Route::get('/sheet', [MonthlyReportController::class, 'show'])->name('show');
            Route::post('/save', [MonthlyReportController::class, 'save'])->name('save');
        });

        // ── Semester Report ────────────────────────────────────────
        Route::prefix('semester-report')->name('semester-report.')->group(function () {
            Route::get('/',      [SemesterReportController::class, 'index'])->name('index');
            Route::get('/sheet', [SemesterReportController::class, 'show'])->name('show');
        });

        // ── Annual Report ──────────────────────────────────────────
        Route::prefix('annual-report')->name('annual-report.')->group(function () {
            Route::get('/',      [AnnualReportController::class, 'index'])->name('index');
            Route::get('/sheet', [AnnualReportController::class, 'show'])->name('show');
        });

        // ── Reports ─────────────────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/ranking',       [ReportController::class, 'rankingIndex'])->name('ranking.index');
            Route::get('/ranking/sheet', [ReportController::class, 'rankingSheet'])->name('ranking.sheet');
            Route::get('/honors',        [ReportController::class, 'honorsIndex'])->name('honors.index');
            Route::get('/honors/sheet',  [ReportController::class, 'honorsSheet'])->name('honors.sheet');
        });
    });
