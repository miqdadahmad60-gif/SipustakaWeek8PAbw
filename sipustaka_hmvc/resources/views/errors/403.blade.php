@extends('layouts.app')
@section('title', '403 Forbidden')

@section('content')
<div class="text-center py-5">
    <h1 class="display-1 fw-bold text-danger">403</h1>
    <h4>Akses Ditolak</h4>
    <p class="text-muted">{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses ke halaman ini.' }}</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
</div>
@endsection
