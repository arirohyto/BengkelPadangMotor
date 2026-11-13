<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MotorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Motor::with(['pelanggan', 'transaksiTerakhir']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_polisi', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%");
                    });
            });
        }
        if ($request->filled('tipe')) {
            $query->where('tipe_motor', $request->tipe);
        }

        $motors = $query->orderBy('created_at', 'desc')->paginate(10);
        $tipeOptions = Motor::TIPE_MOTOR;

        return view('motor.index', compact('motors', 'tipeOptions'));
    }

    public function create(Request $request): View
    {
        $pelangganList = Pelanggan::orderBy('nama')->get();
        $tipeOptions = Motor::TIPE_MOTOR;
        $selectedPelanggan = $request->get('pelanggan_id');

        return view('motor.create', compact('pelangganList', 'tipeOptions', 'selectedPelanggan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id',
            'no_polisi' => 'required|string|max:15|unique:motor,no_polisi',
            'merk' => 'required|string|max:50',
            'tipe_motor' => 'required|in:Matic,Bebek,Sport',
            'tahun_pembuatan' => 'required|integer|min:1990|max:' . date('Y'),
        ], [
            'id_pelanggan.required' => 'Pilih pelanggan',
            'id_pelanggan.exists' => 'Pelanggan tidak valid',
            'no_polisi.required' => 'Nomor polisi wajib diisi',
            'no_polisi.unique' => 'Nomor polisi sudah terdaftar',
            'merk.required' => 'Merk motor wajib diisi',
            'tipe_motor.required' => 'Tipe motor wajib dipilih',
            'tahun_pembuatan.required' => 'Tahun pembuatan wajib diisi',
        ]);

        Motor::create($validated);

        if ($request->has('redirect_to_pelanggan')) {
            return redirect()->route('pelanggan.show', $validated['id_pelanggan'])
                ->with('success', 'Data motor berhasil ditambahkan!');
        }

        return redirect()->route('motor.index')
            ->with('success', 'Data motor berhasil ditambahkan!');
    }

    public function show(Motor $motor): View
    {
        $motor->load([
            'pelanggan',
            'transaksiServis' => function ($q) {
                $q->latest('tanggal_servis')->limit(10);
            }
        ]);

        return view('motor.show', compact('motor'));
    }

    public function edit(Motor $motor): View
    {
        $pelangganList = Pelanggan::orderBy('nama')->get();
        $tipeOptions = Motor::TIPE_MOTOR;

        return view('motor.edit', compact('motor', 'pelangganList', 'tipeOptions'));
    }

    public function update(Request $request, Motor $motor): RedirectResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id',
            'no_polisi' => 'required|string|max:15|unique:motor,no_polisi,' . $motor->id,
            'merk' => 'required|string|max:50',
            'tipe_motor' => 'required|in:Matic,Bebek,Sport',
            'tahun_pembuatan' => 'required|integer|min:1990|max:' . date('Y'),
        ]);

        $motor->update($validated);

        return redirect()->route('motor.index')
            ->with('success', 'Data motor berhasil diperbarui!');
    }
    public function destroy(Motor $motor): RedirectResponse
    {
        $motor->delete();

        return redirect()->route('motor.index')
            ->with('success', 'Data motor berhasil dihapus!');
    }
}
