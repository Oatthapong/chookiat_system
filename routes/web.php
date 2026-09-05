<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\InstallmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ถ้า login เเล้วส่งไป dashboard ถ้ายังไม่ login ให้ส่งไปหน้า login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    // Forgot / Reset Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 2.5 ระบบคำนวณยอดผ่อน
    Route::get('/installments', [InstallmentController::class, 'index'])->name('installments.index');
    Route::post('/installments/calculate', [InstallmentController::class, 'calculate'])->name('installments.calculate');

    // 2.3 ระบบจัดการคลังรถยนต์ (CRUD ด้วย jQuery AJAX)
    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
    Route::get('/cars/{id}', [CarController::class, 'show'])->name('cars.show');
    Route::get('/cars/{id}/edit', [CarController::class, 'edit'])->name('cars.edit');
    Route::put('/cars/{id}', [CarController::class, 'update'])->name('cars.update');
    Route::delete('/cars/{id}', [CarController::class, 'destroy'])->name('cars.destroy');
});
