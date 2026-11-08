@php
    $fields = [
        'judul' => ['Judul', 'text'],
        'penulis' => ['Penulis', 'text'],
        'penerbit' => ['Penerbit', 'text'],
        'tahun_terbit' => ['Tahun Terbit', 'number'],
        'stok' => ['Stok', 'number'],
    ];
@endphp

@foreach($fields as $name => [$label, $type])
    <div class="mb-3">
        <label class="form-label">{{ $label }}</label>
        <input type="{{ $type }}" name="{{ $name }}"
               value="{{ old($name, $book->{$name} ?? '') }}"
               class="form-control @error($name) is-invalid @enderror">
        @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
@endforeach

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('books.index') }}" class="btn btn-secondary">Batal</a>
