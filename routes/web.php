<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// ─── Public root ──────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('teacher.dashboard');
    }
    return redirect()->route('login');
});

// ─── Auth routes (Breeze) ─────────────────────────────────────────
require __DIR__ . '/auth.php';

Route::get('/login/qr', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'loginWithQr'])
    ->name('login.qr');

// ─── Profile routes (shared for admin + teacher) ──────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',              [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit',         [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',              [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar',      [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar',    [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
});

// ─── Role-based route files ───────────────────────────────────────
require __DIR__ . '/admin.php';
require __DIR__ . '/teacher.php';