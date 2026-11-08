<?php

use Illuminate\Support\Facades\Route;
use Modules\Loan\Http\Controllers\LoanController;

// Dapat diakses admin maupun user (harus login)
Route::middleware('auth')->prefix('loans')->name('loans.')->group(function () {
    Route::get('/', [LoanController::class, 'index'])->name('index');
    Route::get('/list', [LoanController::class, 'list'])->name('list');
    Route::post('/', [LoanController::class, 'store'])->name('store');
    Route::get('/{loan}', [LoanController::class, 'show'])->name('show');
    Route::put('/{loan}', [LoanController::class, 'update'])->name('update');
    Route::delete('/{loan}', [LoanController::class, 'destroy'])->name('destroy');
});
