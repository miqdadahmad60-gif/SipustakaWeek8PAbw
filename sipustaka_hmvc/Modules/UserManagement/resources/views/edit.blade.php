@extends('layouts.app')
@section('title', 'Ubah User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Ubah User</h4>
                <form method="POST" action="{{ route('users.update', $user) }}" novalidate>
                    @csrf @method('PUT')
                    @include('usermanagement::_form', ['user' => $user])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
