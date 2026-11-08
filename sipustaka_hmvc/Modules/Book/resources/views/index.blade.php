@extends('layouts.app')
@section('title', 'Daftar Buku')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Daftar Buku</h3>
    @if(auth()->user()->role === 'admin')
        <a href="{{ route('books.create') }}" class="btn btn-primary">+ Tambah Buku</a>
    @endif
</div>

<form method="GET" action="{{ route('books.index') }}" class="row g-2 mb-3">
    <div class="col-md-6">
        <input type="text" name="q" value="{{ $q }}" class="form-control"
               placeholder="Cari judul, penulis, atau penerbit...">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-primary">Cari</button>
        @if($q !== '')
            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th>Tahun Terbit</th>
                    <th>Stok</th>
                    @if(auth()->user()->role === 'admin') <th class="text-end">Aksi</th> @endif
                </tr>
            </thead>
            <tbody>
            @forelse($books as $book)
                <tr>
                    <td>{{ $books->firstItem() + $loop->index }}</td>
                    <td>{{ $book->judul }}</td>
                    <td>{{ $book->penulis }}</td>
                    <td>{{ $book->penerbit }}</td>
                    <td>{{ $book->tahun_terbit }}</td>
                    <td>{{ $book->stok }}</td>
                    @if(auth()->user()->role === 'admin')
                        <td class="text-end">
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-warning">Ubah</a>
                            <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus buku ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Data buku tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $books->links('pagination::bootstrap-5') }}
</div>
@endsection
