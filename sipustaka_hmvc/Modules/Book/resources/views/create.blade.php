@extends('layouts.app')
@section('title', 'Tambah Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Tambah Buku</h4>
                <form method="POST" action="{{ route('books.store') }}" novalidate>
                    @csrf
                    @include('book::_form', ['book' => null])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
