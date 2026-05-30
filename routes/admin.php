<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ClassTeacherController;
use App\Http\Controllers\Admin\AttendanceSessionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExaminationScoreController;
use App\Http\Controllers\Admin\ScoreReportController;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Academic Structure ────────────────────────────────────
        Route::resource('academic-years', AcademicYearController::class);
        Route::patch(
            'academic-years/{academicYear}/activate',
            [AcademicYearController::class, 'activate']
        )->name('academic-years.activate');

        Route::resource('grades',   GradeController::class);
        Route::resource('subjects', SubjectController::class);

        // ── People ────────────────────────────────────────────────
        Route::resource('teachers', TeacherController::class);
        Route::resource('students', StudentController::class);

        // ── Classes ───────────────────────────────────────────────
        Route::resource('classes', SchoolClassController::class);

        Route::prefix('classes/{class}/teachers')
            ->name('classes.teachers.')
            ->group(function () {
                Route::get('/',                         [ClassTeacherController::class, 'index'])->name('index');
                Route::get('/create',                   [ClassTeacherController::class, 'create'])->name('create');
                Route::post('/',                        [ClassTeacherController::class, 'store'])->name('store');
                Route::patch('/{classTeacher}/primary', [ClassTeacherController::class, 'setPrimary'])->name('setPrimary');
                Route::delete('/{classTeacher}',        [ClassTeacherController::class, 'destroy'])->name('destroy');
            });

        // ── Enrollments ───────────────────────────────────────────
        Route::resource('enrollments', EnrollmentController::class);
        Route::patch(
            'enrollments/{enrollment}/status',
            [EnrollmentController::class, 'updateStatus']
        )->name('enrollments.updateStatus');
        Route::post('enrollments/{enrollment}/transfer', [EnrollmentController::class, 'transfer'])->name('enrollments.transfer');
        Route::post('enrollments/{enrollment}/promote',  [EnrollmentController::class, 'promote'])->name('enrollments.promote');

        // ── Attendance ────────────────────────────────────────────
        Route::resource('attendance-sessions', AttendanceSessionController::class)
            ->except(['edit', 'update']);
        Route::get(
            'attendance-sessions/{attendanceSession}/attendance',
            [AttendanceController::class, 'index']
        )->name('attendance-sessions.attendance.index');

        // ── Examination Scores ────────────────────────────────────
        Route::prefix('examination-scores')->name('examination-scores.')->group(function () {
            Route::get('/',                [ExaminationScoreController::class, 'index'])->name('index');
            Route::get('/sheet',           [ExaminationScoreController::class, 'sheet'])->name('sheet');
            Route::post('/save-monthly',   [ExaminationScoreController::class, 'saveMonthly'])->name('save-monthly');
            Route::post('/save-semester',  [ExaminationScoreController::class, 'saveSemester'])->name('save-semester');
            Route::post('/lock-monthly',   [ExaminationScoreController::class, 'lockMonthly'])->name('lock-monthly');
            Route::post('/unlock-monthly', [ExaminationScoreController::class, 'unlockMonthly'])->name('unlock-monthly');
            Route::post('/lock-semester',  [ExaminationScoreController::class, 'lockSemester'])->name('lock-semester');
            Route::post('/unlock-semester', [ExaminationScoreController::class, 'unlockSemester'])->name('unlock-semester');
        });

        // ── Score Report ──────────────────────────────────────────
        Route::prefix('score-report')->name('score-report.')->group(function () {
            Route::get('/',     [ScoreReportController::class, 'index'])->name('index');
            Route::get('/show', [ScoreReportController::class, 'show'])->name('show');
        });
    });
