<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AttendanceSessionController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\StudentAttendanceController;
use App\Http\Controllers\Teacher\ScoreController;
use App\Http\Controllers\Teacher\ReportController;
use App\Http\Controllers\Teacher\StudentController;

Route::middleware(['auth', 'verified', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Student Attendance ────────────────────────────────────
        Route::prefix('attendance')->name('student-attendance.')->group(function () {
            Route::get('/',      [StudentAttendanceController::class, 'index'])->name('index');
            Route::post('/save', [StudentAttendanceController::class, 'save'])->name('save');
        });

        // ── Scores (consolidated) ─────────────────────────────────
        Route::prefix('scores')->name('scores.')->group(function () {
            Route::get('/',       [ScoreController::class, 'index'])->name('index');
            Route::get('/input',  [ScoreController::class, 'input'])->name('input');
            Route::post('/save',  [ScoreController::class, 'save'])->name('save');
            Route::get('/report', [ScoreController::class, 'report'])->name('report');
        });

        // ── Reports (unified) ─────────────────────────────────────
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',      [ReportController::class, 'index'])->name('index');
            Route::get('/print', [ReportController::class, 'print'])->name('print');
        });

        // ── Students (view & edit only) ───────────────────────────
        Route::resource('students', StudentController::class)
            ->only(['index', 'show', 'edit', 'update']);
    });