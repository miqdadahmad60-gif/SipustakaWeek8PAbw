<?php

use Illuminate\Support\Facades\Route;
use Modules\Book\Http\Controllers\BookController;

Route::middleware('auth')->group(function () {
    // Admin & user: melihat daftar buku
    Route::get('/books', [BookController::class, 'index'])->name('books.index');

    // Hanya admin: tambah, ubah, hapus
    Route::middleware('role:admin')->group(function () {
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    });
});
