<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SparepartController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sparepart::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_part', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_komponen', $request->kategori);
        }

        if ($request->filled('low_stock')) {
            $query->where('stok', '<', 5);
        }

        $spareparts = $query->orderBy('nama_barang', 'asc')->paginate(10);

        $kategori = Sparepart::whereNotNull('kategori_komponen')
            ->where('kategori_komponen', '!=', '')
            ->distinct()
            ->pluck('kategori_komponen');

        return view('sparepart.index', compact('spareparts', 'kategori'));
    }
    public function create(): View
    {
        return view('sparepart.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_part' => 'required|string|max:20|unique:spareparts,kode_part',
            'nama_barang' => 'required|string|max:100',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ], [
            'kode_part.required' => 'Kode part wajib diisi',
            'kode_part.unique' => 'Kode part sudah ada',
            'nama_barang.required' => 'Nama barang wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'harga_beli.required' => 'Harga beli wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
        ]);

        Sparepart::create($validated);

        return redirect()->route('sparepart.index')
            ->with('success', 'Data sparepart berhasil ditambahkan!');
    }

    public function show(Sparepart $sparepart): View
    {
        return view('sparepart.show', compact('sparepart'));
    }
    public function edit(Sparepart $sparepart): View
    {
        return view('sparepart.edit', compact('sparepart'));
    }
    public function update(Request $request, Sparepart $sparepart): RedirectResponse
    {
        $validated = $request->validate([
            'kode_part' => 'required|string|max:20|unique:spareparts,kode_part,' . $sparepart->id,
            'nama_barang' => 'required|string|max:100',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $sparepart->update($validated);

        return redirect()->route('sparepart.index')
            ->with('success', 'Data sparepart berhasil diperbarui!');
    }
    public function destroy(Sparepart $sparepart): RedirectResponse
    {
        $sparepart->delete();

        return redirect()->route('sparepart.index')
            ->with('success', 'Data sparepart berhasil dihapus!');
    }
    public function updateStock(Request $request, Sparepart $sparepart): RedirectResponse
    {
        $validated = $request->validate([
            'stok_tambah' => 'required|integer',
        ]);

        $sparepart->stok += $validated['stok_tambah'];
        $sparepart->save();

        return redirect()->back()
            ->with('success', 'Stok berhasil diperbarui!');
    }
    public function search(Request $request)
    {
        $search = $request->get('q', '');

        $spareparts = Sparepart::where('nama_barang', 'like', "%{$search}%")
            ->orWhere('kode_part', 'like', "%{$search}%")
            ->where('stok', '>', 0)
            ->limit(10)
            ->get(['id', 'kode_part', 'nama_barang', 'stok', 'harga_jual']);

        return response()->json($spareparts);
    }
}
