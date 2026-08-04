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
use App\Http\Controllers\Admin\StudentAttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScoreController;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Academic Structure ────────────────────────────────────
        Route::resource('academic-years', AcademicYearController::class);
        Route::patch('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])
            ->name('academic-years.activate');
        Route::resource('grades',   GradeController::class);
        Route::resource('subjects', SubjectController::class);
        Route::post('subjects/template', [SubjectController::class, 'storeTemplate'])
            ->name('subjects.template');

        // ── People ────────────────────────────────────────────────
        Route::resource('teachers', TeacherController::class);

        Route::get('students/archived',             [StudentController::class, 'archived'])->name('students.archived');
        Route::post('students/{id}/restore',        [StudentController::class, 'restore'])->name('students.restore');
        Route::delete('students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
        Route::resource('students', StudentController::class);

        // ── Classes ───────────────────────────────────────────────
        Route::resource('classes', SchoolClassController::class);
        Route::prefix('classes/{class}/teachers')->name('classes.teachers.')->group(function () {
            Route::get('/',                         [ClassTeacherController::class, 'index'])->name('index');
            Route::get('/create',                   [ClassTeacherController::class, 'create'])->name('create');
            Route::post('/',                        [ClassTeacherController::class, 'store'])->name('store');
            Route::patch('/{classTeacher}/primary', [ClassTeacherController::class, 'setPrimary'])->name('setPrimary');
            Route::delete('/{classTeacher}',        [ClassTeacherController::class, 'destroy'])->name('destroy');
        });

        // ── Enrollments ───────────────────────────────────────────
        Route::resource('enrollments', EnrollmentController::class);
        Route::patch('enrollments/{enrollment}/status',  [EnrollmentController::class, 'updateStatus'])->name('enrollments.updateStatus');
        Route::post('enrollments/{enrollment}/transfer', [EnrollmentController::class, 'transfer'])->name('enrollments.transfer');
        Route::post('enrollments/{enrollment}/promote',  [EnrollmentController::class, 'promote'])->name('enrollments.promote');

        // ── Student Attendance ────────────────────────────────────
        Route::prefix('attendance')->name('student-attendance.')->group(function () {
            Route::get('/',      [StudentAttendanceController::class, 'index'])->name('index');
            Route::post('/save', [StudentAttendanceController::class, 'save'])->name('save');
        });

        // ── Scores (consolidated) ─────────────────────────────────
        Route::prefix('scores')->name('scores.')->group(function () {
            Route::get('/',        [ScoreController::class, 'index'])->name('index');
            Route::get('/input',   [ScoreController::class, 'input'])->name('input');
            Route::post('/save',   [ScoreController::class, 'save'])->name('save');
            Route::post('/lock',   [ScoreController::class, 'lock'])->name('lock');
            Route::post('/unlock', [ScoreController::class, 'unlock'])->name('unlock');
            Route::get('/report',  [ScoreController::class, 'report'])->name('report');
        });

        // ── Reports (unified) ─────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',      [ReportController::class, 'index'])->name('index');
            Route::get('/print', [ReportController::class, 'print'])->name('print');
        });

        // ── User QR Code ──────────────────────────────────────────
        Route::post('users/{user}/regenerate-qr', function (\App\Models\User $user) {
            $user->generateLoginToken();
            return back()->with('success', 'QR Code បានផ្លាស់ប្តូរដោយជោគជ័យ។');
        })->name('users.regenerate-qr');
    });