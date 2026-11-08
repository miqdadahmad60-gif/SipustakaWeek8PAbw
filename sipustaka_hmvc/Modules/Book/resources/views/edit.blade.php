@extends('layouts.app')
@section('title', 'Ubah Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Ubah Buku</h4>
                <form method="POST" action="{{ route('books.update', $book) }}" novalidate>
                    @csrf @method('PUT')
                    @include('book::_form', ['book' => $book])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
