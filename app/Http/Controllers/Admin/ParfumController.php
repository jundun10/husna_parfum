<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parfum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ParfumController extends Controller
{
    public function index(): Response
    {
        $parfums = Parfum::latest()->get();

        return Inertia::render('Admin/Stok', [
            'parfums' => $parfums,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'kategori' => [
            'required',
            'in:Pria,Wanita,Unisex,Parfum Lain',
        ],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('parfum', 'public');
        }

        Parfum::create($validated);

        return back()->with('success', 'Stok parfum berhasil ditambahkan.');
    }

    public function update(Request $request, Parfum $parfum): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'kategori' => [
            'required',
            'in:Pria,Wanita,Unisex,Parfum Lain',
        ],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('parfum', 'public');
        }

        $parfum->update($validated);

        return back()->with('success', 'Stok parfum berhasil diperbarui.');
    }

    public function destroy(Parfum $parfum): RedirectResponse
    {
        $parfum->delete();

        return back()->with('success', 'Stok parfum berhasil dihapus.');
    }
}