@php $self = $user && $user->id === auth()->id(); @endphp

<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
           class="form-control @error('email') is-invalid @enderror">
    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Password @if($user) <small class="text-muted">(kosongkan jika tidak diubah)</small> @endif</label>
    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Konfirmasi Password</label>
    <input type="password" name="password_confirmation" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Role</label>
    {{-- Admin tidak bisa mengubah role akunnya sendiri: select dinonaktifkan, nilai dikirim lewat hidden input --}}
    <select name="role" class="form-select @error('role') is-invalid @enderror" @disabled($self)>
        @foreach(['admin', 'user'] as $r)
            <option value="{{ $r }}" @selected(old('role', $user->role ?? 'user') === $r)>{{ ucfirst($r) }}</option>
        @endforeach
    </select>
    @if($self)
        <input type="hidden" name="role" value="{{ $user->role }}">
        <div class="form-text">Anda tidak dapat mengubah role akun Anda sendiri.</div>
    @endif
    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
