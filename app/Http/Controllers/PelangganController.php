<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pelanggan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jumlah_motor')) {
            $jumlah = $request->jumlah_motor;
            if ($jumlah === '3+') {
                $query->has('motor', '>=', 3);
            } else {
                $query->has('motor', '=', (int)$jumlah);
            }
        }

        $query->with(['motor', 'transaksi' => function($q) {
            $q->latest('tanggal_servis')->limit(1);
        }]);

        $pelanggan = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create(): View
    {
        return view('pelanggan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'nullable|string',
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi',
            'no_hp.required' => 'Nomor HP wajib diisi',
        ]);

        Pelanggan::create($validated);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan!');
    }

    public function show(Pelanggan $pelanggan): View
    {
        $pelanggan->load([
            'motors',
            'motor',
            'transaksi',
            'transaksiServis' => function ($q) {
                $q->latest('tanggal_servis')->limit(10);
            }
        ]);

        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan): View
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus!');
    }
    public function getMotors(Pelanggan $pelanggan)
    {
        return response()->json([
            'pelanggan' => $pelanggan,
            'motors' => $pelanggan->motors
        ]);
    }
}
