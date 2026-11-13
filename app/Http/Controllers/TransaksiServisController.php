<?php

namespace App\Http\Controllers;

use App\Models\TransaksiServis;
use App\Models\Pelanggan;
use App\Models\Motor;
use App\Models\Sparepart;
use App\Services\CARTPredictor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class TransaksiServisController extends Controller
{
    protected CARTPredictor $predictor;

    public function __construct()
    {
        $this->predictor = new CARTPredictor();
    }

    public function index(Request $request): View
    {
        $query = TransaksiServis::with(['pelanggan', 'motor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pelanggan', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%");
                })->orWhereHas('motor', function ($q2) use ($search) {
                    $q2->where('no_polisi', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_servis', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_servis', '<=', $request->sampai_tanggal);
        }

        $totalPendapatan = $query->sum('total_biaya');

        $transaksi = $query->orderBy('tanggal_servis', 'desc')->paginate(10);
        $statusOptions = TransaksiServis::STATUS;

        return view('transaksi.index', compact('transaksi', 'statusOptions', 'totalPendapatan'));
    }

    public function create(Request $request): View
    {
        $pelangganList = Pelanggan::orderBy('nama')->get();
        $kategoriKeluhan = TransaksiServis::KATEGORI_KELUHAN;
        $jenisServisList = TransaksiServis::JENIS_SERVIS;
        $sparepartList = Sparepart::where('stok', '>', 0)->orderBy('nama_barang')->get();

        $selectedPelanggan = null;
        $selectedMotor = null;
        $lastServis = null;

        if ($request->has('pelanggan_id')) {
            $selectedPelanggan = Pelanggan::with('motors')->find($request->pelanggan_id);
        }
        if ($request->has('motor_id')) {
            $selectedMotor = Motor::with('pelanggan')->find($request->motor_id);
            if ($selectedMotor) {
                $selectedPelanggan = $selectedMotor->pelanggan;
                $lastServis = TransaksiServis::where('id_motor', $selectedMotor->id)
                    ->latest('tanggal_servis')
                    ->first();
            }
        }

        return view('transaksi.create', compact(
            'pelangganList',
            'kategoriKeluhan',
            'jenisServisList',
            'sparepartList',
            'selectedPelanggan',
            'selectedMotor',
            'lastServis'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|exists:pelanggan,id',
            'id_motor' => 'required|exists:motor,id',
            'tanggal_servis' => 'required|date',
            'km_saat_ini' => 'required|integer|min:0',
            'kategori_keluhan' => 'required|in:' . implode(',', TransaksiServis::KATEGORI_KELUHAN),
            'jenis_servis' => 'required|in:' . implode(',', TransaksiServis::JENIS_SERVIS),
            'keluhan_konsumen' => 'nullable|string',
            'tindakan_mekanik' => 'nullable|string',
            'biaya_jasa' => 'required|numeric|min:0',
            'spareparts' => 'nullable|array',
            'spareparts.*.id' => 'exists:spareparts,id',
            'spareparts.*.jumlah' => 'integer|min:1',
        ]);

        $motor = Motor::findOrFail($validated['id_motor']);
        $usiaMotor = Carbon::now()->year - $motor->tahun_pembuatan;

        $lastServis = TransaksiServis::where('id_motor', $motor->id)
            ->latest('tanggal_servis')
            ->first();

        $tanggalServisTerakhir = $lastServis ? $lastServis->tanggal_servis : null;

        $durasiHari = 0;
        if ($tanggalServisTerakhir) {
            $durasiHari = Carbon::parse($tanggalServisTerakhir)->diffInDays(Carbon::parse($validated['tanggal_servis']));
        }

        // input feature projection
        $projectedKm = $validated['km_saat_ini'] + 3000;
        $projectedDurasi = 90;

        if ($validated['jenis_servis'] === 'Ganti Part Fast Moving') {
            $lastRoutineServis = TransaksiServis::where('id_motor', $motor->id)
                ->whereIn('jenis_servis', ['Servis Ringan', 'Servis CVT', 'Servis Besar'])
                ->latest('tanggal_servis')
                ->first();

            if ($lastRoutineServis) {
                $daysSinceRoutine = Carbon::parse($lastRoutineServis->tanggal_servis)->diffInDays(Carbon::parse($validated['tanggal_servis']));
                if ($daysSinceRoutine < 90) {
                    $projectedDurasi = 90 - $daysSinceRoutine;
                }
            }
        }

        $replacedToday = [];
        if (isset($validated['spareparts']) && is_array($validated['spareparts'])) {
            $spIds = array_column($validated['spareparts'], 'id');
            $replacedToday = \DB::table('spareparts')
                ->whereIn('id', $spIds)
                ->whereNotNull('kategori_komponen')
                ->pluck('kategori_komponen')
                ->toArray();
        }

        if ($validated['jenis_servis'] === 'Servis CVT') {
            $replacedToday = array_unique(array_merge($replacedToday, ['V-Belt', 'Roller CVT']));
        }


        $replacementHistory = \DB::table('detail_sparepart_transaksi')
            ->join('transaksi_servis', 'detail_sparepart_transaksi.id_transaksi', '=', 'transaksi_servis.id')
            ->join('spareparts', 'detail_sparepart_transaksi.id_sparepart', '=', 'spareparts.id')
            ->where('transaksi_servis.id_motor', $motor->id)
            ->whereNotNull('spareparts.kategori_komponen')
            ->select('spareparts.kategori_komponen', \DB::raw('MAX(transaksi_servis.km_saat_ini) as max_km'))
            ->groupBy('spareparts.kategori_komponen')
            ->pluck('max_km', 'kategori_komponen')
            ->toArray();

        $kritis = [];
        $totalEvaluated = 0;
        foreach (TransaksiServis::KSB_LIFECYCLE as $komponen => $limitKm) {
            if ($komponen === 'V-Belt' || $komponen === 'Roller CVT') {
                if ($motor->tipe_motor !== 'Matic') {
                    continue;
                }
            }
            if ($komponen === 'Rantai Gear') {
                if ($motor->tipe_motor === 'Matic') {
                    continue;
                }
            }

            if (in_array($komponen, $replacedToday)) {
                continue;
            }

            $totalEvaluated++;
            $lastKm = $replacementHistory[$komponen] ?? 0;
            $deltaKm = $projectedKm - $lastKm;

            if ($deltaKm >= $limitKm) {
                $kritis[] = [
                    'nama' => $komponen,
                    'delta_km' => $deltaKm,
                    'limit_km' => $limitKm,
                    'last_replaced_km' => $lastKm,
                ];
            }
        }

        $projectedKeluhan = 'Rutin';
        if (!empty($kritis)) {
            $firstKritis = $kritis[0]['nama'];
            $projectedKeluhan = match ($firstKritis) {
                'Busi' => 'Performa',
                'Kampas Rem' => 'Pengereman',
                'Filter Udara' => 'Performa',
                'Aki' => 'Kelistrikan',
                'V-Belt', 'Roller CVT' => 'CVT',
                'Rantai Gear' => 'Rantai',
                default => 'Rutin'
            };
        }

        $prediksi = $this->predictor->predict(
            $projectedDurasi,
            $projectedKm,
            $usiaMotor,
            $projectedKeluhan,
            $motor->tipe_motor
        );

        $rekomendasi = $prediksi['rekomendasi'];
        if (!empty($kritis)) {
            if (count($kritis) >= $totalEvaluated && $totalEvaluated >= 4) {
                $rekomendasi = 'Servis Besar';
            } else {
                $hasCVT = collect($kritis)->contains(fn($item) => in_array($item['nama'], ['V-Belt', 'Roller CVT']));
                if ($rekomendasi === 'Servis Ringan') {
                    $rekomendasi = $hasCVT ? 'Servis CVT' : 'Ganti Part Fast Moving';
                } elseif ($rekomendasi === 'Ganti Part Fast Moving' && $hasCVT) {
                    $rekomendasi = 'Servis CVT';
                } elseif ($rekomendasi === 'Servis CVT' && !$hasCVT) {
                    $rekomendasi = 'Ganti Part Fast Moving';
                }
            }
        } else {
            if ($rekomendasi === 'Ganti Part Fast Moving') {
                if ($motor->tipe_motor === 'Matic' && $validated['km_saat_ini'] > 20000) {
                    $rekomendasi = 'Servis CVT';
                } else {
                    $rekomendasi = 'Servis Ringan';
                }
            }
        }

        if ($validated['jenis_servis'] === 'Servis Besar') {
            $rekomendasi = $motor->tipe_motor === 'Matic' ? 'Servis CVT' : 'Servis Ringan';
        }

        $totalSparepart = 0;
        if (isset($validated['spareparts']) && is_array($validated['spareparts'])) {
            foreach ($validated['spareparts'] as $sp) {
                $sparepart = Sparepart::find($sp['id']);
                if ($sparepart) {
                    $totalSparepart += $sparepart->harga_jual * $sp['jumlah'];
                }
            }
        }

        $transaksi = TransaksiServis::create([
            'id_kasir' => auth()->id(),
            'id_pelanggan' => $validated['id_pelanggan'],
            'id_motor' => $validated['id_motor'],
            'tanggal_servis' => $validated['tanggal_servis'],
            'tanggal_servis_terakhir' => $tanggalServisTerakhir,
            'km_saat_ini' => $validated['km_saat_ini'],
            'durasi_hari' => $durasiHari,
            'usia_motor' => $usiaMotor,
            'kategori_keluhan' => $validated['kategori_keluhan'],
            'jenis_servis' => $validated['jenis_servis'],
            'keluhan_konsumen' => $validated['keluhan_konsumen'],
            'tindakan_mekanik' => $validated['tindakan_mekanik'],
            'rekomendasi_servis' => $rekomendasi,
            'biaya_jasa' => $validated['biaya_jasa'],
            'total_biaya' => $validated['biaya_jasa'] + $totalSparepart,
            'status' => 'diterima',
        ]);

        if (isset($validated['spareparts']) && is_array($validated['spareparts'])) {
            foreach ($validated['spareparts'] as $sp) {
                $sparepart = Sparepart::find($sp['id']);
                if ($sparepart) {
                    $subtotal = $sparepart->harga_jual * $sp['jumlah'];
                    $transaksi->spareparts()->attach($sp['id'], [
                        'jumlah' => $sp['jumlah'],
                        'harga_satuan' => $sparepart->harga_jual,
                        'subtotal' => $subtotal,
                    ]);
                    $sparepart->decrement('stok', $sp['jumlah']);
                }
            }
        }

        return redirect()->route('transaksi.show', $transaksi)
            ->with('success', 'Transaksi servis berhasil disimpan! Rekomendasi Selanjutnya: ' . $rekomendasi);
    }


    public function show(TransaksiServis $transaksi): View
    {
        $transaksi->load(['pelanggan', 'motor', 'spareparts', 'kasir']);
        $rekomendasiDescription = CARTPredictor::getRekomendasiDescription($transaksi->rekomendasi_servis);

        return view('transaksi.show', compact('transaksi', 'rekomendasiDescription'));
    }

    public function edit(TransaksiServis $transaksi): View
    {
        $transaksi->load(['pelanggan', 'motor', 'spareparts']);
        $pelangganList = Pelanggan::orderBy('nama')->get();
        $kategoriKeluhan = TransaksiServis::KATEGORI_KELUHAN;
        $statusOptions = TransaksiServis::STATUS;
        $sparepartList = Sparepart::orderBy('nama_barang')->get();

        return view('transaksi.edit', compact(
            'transaksi',
            'pelangganList',
            'kategoriKeluhan',
            'statusOptions',
            'sparepartList'
        ));
    }

    public function update(Request $request, TransaksiServis $transaksi): RedirectResponse
    {
        $validated = $request->validate([
            'tindakan_mekanik' => 'nullable|string',
            'biaya_jasa' => 'required|numeric|min:0',
        ]);

        $totalSparepart = $transaksi->spareparts->sum('pivot.subtotal');

        $newStatus = $transaksi->status;
        if ($transaksi->status === 'diterima' && !empty($validated['tindakan_mekanik'])) {
            $newStatus = 'dikerjakan';
        }

        $transaksi->update([
            'tindakan_mekanik' => $validated['tindakan_mekanik'],
            'biaya_jasa' => $validated['biaya_jasa'],
            'total_biaya' => $validated['biaya_jasa'] + $totalSparepart,
            'status' => $newStatus,
        ]);

        return redirect()->route('transaksi.show', $transaksi)
            ->with('success', 'Transaksi servis berhasil diperbarui! Status: ' . ucfirst($newStatus));
    }

    public function destroy(TransaksiServis $transaksi): RedirectResponse
    {
        foreach ($transaksi->spareparts as $sparepart) {
            $sparepart->increment('stok', $sparepart->pivot->jumlah);
        }

        $transaksi->delete();

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi servis berhasil dihapus!');
    }

    public function updateStatus(Request $request, TransaksiServis $transaksi): RedirectResponse
    {
        $statusFlow = ['diterima' => 'dikerjakan', 'dikerjakan' => 'selesai'];
        $currentStatus = $transaksi->status;

        if (!isset($statusFlow[$currentStatus])) {
            return redirect()->back()
                ->with('info', 'Transaksi sudah berstatus "Selesai" dan tidak dapat diubah lagi.');
        }

        $nextStatus = $statusFlow[$currentStatus];
        $transaksi->update(['status' => $nextStatus]);

        $messages = [
            'dikerjakan' => 'Motor mulai dikerjakan oleh mekanik.',
            'selesai' => 'Servis selesai! Pembayaran telah dikonfirmasi. Nota siap dicetak.',
        ];

        return redirect()->back()
            ->with('success', $messages[$nextStatus]);
    }

    public function nota(TransaksiServis $transaksi)
    {
        if ($transaksi->status !== 'selesai') {
            return redirect()->route('transaksi.show', $transaksi)
                ->with('error', 'Nota hanya dapat dicetak setelah status servis selesai.');
        }

        $transaksi->load(['pelanggan', 'motor', 'spareparts', 'kasir']);
        $rekomendasiDescription = CARTPredictor::getRekomendasiDescription($transaksi->rekomendasi_servis);
        return view('transaksi.nota', compact('transaksi', 'rekomendasiDescription'));
    }

    public function getRiwayatMotor($motor_id)
    {
        $motor = Motor::findOrFail($motor_id);
        
        $riwayat = TransaksiServis::where('id_motor', $motor->id)
            ->orderBy('tanggal_servis', 'desc')
            ->get();
            
        $totalServis = $riwayat->count();
        
        $lastServis = $riwayat->first();
        
        $riwayatList = $riwayat->take(5)->map(function ($r) {
            return [
                'tanggal_servis_formatted' => $r->tanggal_servis->format('d M Y'),
                'jenis_servis' => $r->jenis_servis ?: 'Servis Berkala',
                'kategori_keluhan' => $r->kategori_keluhan,
                'km_saat_ini' => number_format($r->km_saat_ini, 0, ',', '.'),
                'status' => $r->status,
                'status_badge' => $r->status_badge,
                'tindakan_mekanik' => $r->tindakan_mekanik ? \Str::limit($r->tindakan_mekanik, 45) : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'motor' => [
                    'id' => $motor->id,
                    'merk' => $motor->merk,
                    'no_polisi' => $motor->no_polisi,
                    'tipe_motor' => $motor->tipe_motor,
                ],
                'total_servis' => $totalServis,
                'km_terakhir' => $lastServis ? number_format($lastServis->km_saat_ini, 0, ',', '.') . ' KM' : '-',
                'tanggal_terakhir' => $lastServis ? $lastServis->tanggal_servis->format('d M Y') : '-',
                'riwayat' => $riwayatList,
            ]
        ]);
    }
}
