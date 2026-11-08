<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Route modul (Book, Loan, UserManagement) dimuat oleh ServiceProvider masing-masing modul.

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('books.index')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
