<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);

        return view('usermanagement::index', compact('users'));
    }

    public function create()
    {
        return view('usermanagement::create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('usermanagement::edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']); // password tidak diubah
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // Admin tidak dapat menurunkan role akunnya sendiri
        if ($user->id === auth()->id()) {
            unset($data['role']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diubah.');
    }

    public function destroy(User $user)
    {
        // Admin tidak dapat menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
