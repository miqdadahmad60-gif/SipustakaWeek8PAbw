@extends('layouts.app')
@section('title', 'Tambah User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Tambah User</h4>
                <form method="POST" action="{{ route('users.store') }}" novalidate>
                    @csrf
                    @include('usermanagement::_form', ['user' => null])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
