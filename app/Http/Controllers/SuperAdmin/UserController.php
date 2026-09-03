<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::latest()->get([
            'id',
            'name',
            'email',
            'role',
            'created_at',
        ]);

        return Inertia::render('SuperAdmin/Pengguna', [
            'users' => $users,
            'authUser' => request()->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'admin',
        ]);

        return back()->with(
            'success',
            'Admin berhasil ditambahkan.'
        );
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:user,admin,super_admin'],
        ]);

        if ($user->id === request()->user()->id) {
            return back()->with(
                'error',
                'Role akun sendiri tidak dapat diubah.'
            );
        }

        $user->update([
            'role' => $validated['role'],
        ]);

        return back()->with(
            'success',
            'Role pengguna berhasil diperbarui.'
        );
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === request()->user()->id) {
            return back()->with(
                'error',
                'Akun sendiri tidak dapat dihapus.'
            );
        }

        $user->delete();

        return back()->with(
            'success',
            'Pengguna berhasil dihapus.'
        );
    }
}