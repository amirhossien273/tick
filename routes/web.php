<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\UserStatusController;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::middleware(['auth', EnsureUserIsActive::class])->group(function (): void {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/users/{user}/password', [UserPasswordController::class, 'edit'])
        ->name('users.password.edit');
    Route::patch('/users/{user}/password', [UserPasswordController::class, 'update'])
        ->name('users.password.update');
    Route::patch('/users/{user}/status', UserStatusController::class)
        ->name('users.status.update');
    Route::resource('users', UserController::class)->except('destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
