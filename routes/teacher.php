<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\AttendanceSessionController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\ExaminationScoreController;
use App\Http\Controllers\Teacher\ScoreReportController;

Route::middleware(['auth', 'verified', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Attendance ────────────────────────────────────────────
        Route::resource('attendance-sessions', AttendanceSessionController::class)
            ->except(['edit', 'update']);
        Route::get(
            'attendance-sessions/{attendanceSession}/attendance',
            [AttendanceController::class, 'index']
        )->name('attendance-sessions.attendance.index');
        Route::post(
            'attendance-sessions/{attendanceSession}/attendance',
            [AttendanceController::class, 'store']
        )->name('attendance-sessions.attendance.store');
        Route::patch(
            'attendance-sessions/{attendanceSession}/attendance/{attendance}',
            [AttendanceController::class, 'update']
        )->name('attendance-sessions.attendance.update');

        // ── Examination Scores ────────────────────────────────────
        Route::prefix('examination-scores')->name('examination-scores.')->group(function () {
            Route::get('/',               [ExaminationScoreController::class, 'index'])       ->name('index');
            Route::get('/sheet',          [ExaminationScoreController::class, 'sheet'])       ->name('sheet');
            Route::post('/save-monthly',  [ExaminationScoreController::class, 'saveMonthly']) ->name('save-monthly');
            Route::post('/save-semester', [ExaminationScoreController::class, 'saveSemester'])->name('save-semester');
        });

        // ── Score Report ──────────────────────────────────────────
        Route::prefix('score-report')->name('score-report.')->group(function () {
            Route::get('/',     [ScoreReportController::class, 'index'])->name('index');
            Route::get('/show', [ScoreReportController::class, 'show'])->name('show');
        });
    });