<?php

use Illuminate\Support\Facades\Route;

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

// ─── Role-based route files ───────────────────────────────────────
require __DIR__ . '/admin.php';
require __DIR__ . '/teacher.php';