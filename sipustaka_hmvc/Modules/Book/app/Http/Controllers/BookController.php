<?php

namespace Modules\Book\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Book\Http\Requests\BookRequest;
use Modules\Book\Models\Book;

class BookController extends Controller
{
    /** Daftar buku + pencarian + pagination (admin & user). */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $books = Book::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('judul', 'like', "%{$q}%")
                        ->orWhere('penulis', 'like', "%{$q}%")
                        ->orWhere('penerbit', 'like', "%{$q}%");
                });
            })
            ->orderBy('judul')
            ->paginate(5)
            ->withQueryString();

        return view('book::index', compact('books', 'q'));
    }

    public function create()
    {
        return view('book::create');
    }

    public function store(BookRequest $request)
    {
        Book::create($request->validated());

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        return view('book::edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return redirect()->route('books.index')->with('success', 'Buku berhasil diubah.');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
