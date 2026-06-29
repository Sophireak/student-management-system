<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AttendanceSessionController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\StudentAttendanceController;
use App\Http\Controllers\Teacher\ExaminationScoreController;
use App\Http\Controllers\Teacher\MonthlyReportController;
use App\Http\Controllers\Teacher\SemesterReportController;
use App\Http\Controllers\Teacher\AnnualReportController;
use App\Http\Controllers\Teacher\ReportController;
use App\Http\Controllers\Teacher\StudentController;


Route::middleware(['auth', 'verified', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Student Attendance ────────────────────────────────────
        Route::prefix('student-attendance')->name('student-attendance.')->group(function () {
            Route::get('/',      [StudentAttendanceController::class, 'index'])->name('index');
            Route::get('/sheet', [StudentAttendanceController::class, 'sheet'])->name('sheet');
            Route::post('/save', [StudentAttendanceController::class, 'save'])->name('save');
        });

        // ── Examination Scores ────────────────────────────────────
        Route::prefix('examination-scores')->name('examination-scores.')->group(function () {
            Route::get('/',               [ExaminationScoreController::class, 'index'])->name('index');
            Route::get('/sheet',          [ExaminationScoreController::class, 'sheet'])->name('sheet');
            Route::post('/save-monthly',  [ExaminationScoreController::class, 'saveMonthly'])->name('save-monthly');
            Route::post('/save-semester', [ExaminationScoreController::class, 'saveSemester'])->name('save-semester');
        });

        // ── Score Entry ───────────────────────────────────────────
        Route::prefix('monthly-report')->name('monthly-report.')->group(function () {
            Route::get('/',      [MonthlyReportController::class, 'index'])->name('index');
            Route::get('/sheet', [MonthlyReportController::class, 'show'])->name('show');
            Route::post('/save', [MonthlyReportController::class, 'save'])->name('save');
        });
        Route::prefix('semester-report')->name('semester-report.')->group(function () {
            Route::get('/',      [SemesterReportController::class, 'index'])->name('index');
            Route::get('/sheet', [SemesterReportController::class, 'show'])->name('show');
        });
        Route::prefix('annual-report')->name('annual-report.')->group(function () {
            Route::get('/',      [AnnualReportController::class, 'index'])->name('index');
            Route::get('/sheet', [AnnualReportController::class, 'show'])->name('show');
        });

        // ── Reports ───────────────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/ranking',       [ReportController::class, 'rankingIndex'])->name('ranking.index');
            Route::get('/ranking/sheet', [ReportController::class, 'rankingSheet'])->name('ranking.sheet');
            Route::get('/honors',        [ReportController::class, 'honorsIndex'])->name('honors.index');
            Route::get('/honors/sheet',  [ReportController::class, 'honorsSheet'])->name('honors.sheet');
        });

        // ── Students (view & edit only) ───────────────────────────
        Route::resource('students', StudentController::class)
            ->only(['index', 'show', 'edit', 'update']);
    });
