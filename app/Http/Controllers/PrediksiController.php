<?php

namespace App\Http\Controllers;

use App\Models\Motor;
use App\Models\TransaksiServis;
use App\Services\CARTPredictor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    protected CARTPredictor $predictor;

    public function __construct()
    {
        $this->predictor = new CARTPredictor();
    }

    public function predict(Request $request)
    {
        $validated = $request->validate([
            'motor_id' => 'required|exists:motor,id',
            'km_saat_ini' => 'required|integer|min:0',
            'kategori_keluhan' => 'required|string',
            'jenis_servis' => 'required|string',
            'tanggal_servis' => 'required|date',
            'spareparts' => 'nullable|array',
            'spareparts.*.id' => 'exists:spareparts,id',
            'spareparts.*.jumlah' => 'integer|min:1',
        ]);
        $motor = Motor::findOrFail($validated['motor_id']);
        $usiaMotor = Carbon::now()->year - $motor->tahun_pembuatan;
        
        $lastServis = TransaksiServis::where('id_motor', $motor->id)
            ->latest('tanggal_servis')
            ->first();
            
        $tanggalServisTerakhir = null;
        $durasiHari = 0;
        if ($lastServis) {
            $tanggalServisTerakhir = $lastServis->tanggal_servis->format('Y-m-d');
            $durasiHari = $lastServis->tanggal_servis->diffInDays(Carbon::parse($validated['tanggal_servis']));
        }

        // FORWARD-FEEDING
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

        // 1. Identify which parts are replaced TODAY in this transaction form (to exclude them)
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


        // 2. Get all spareparts replaced in transactions for this motor PRIOR to this one
        $replacementHistory = \DB::table('detail_sparepart_transaksi')
            ->join('transaksi_servis', 'detail_sparepart_transaksi.id_transaksi', '=', 'transaksi_servis.id')
            ->join('spareparts', 'detail_sparepart_transaksi.id_sparepart', '=', 'spareparts.id')
            ->where('transaksi_servis.id_motor', $motor->id)
            ->whereNotNull('spareparts.kategori_komponen')
            ->select('spareparts.kategori_komponen', \DB::raw('MAX(transaksi_servis.km_saat_ini) as max_km'))
            ->groupBy('spareparts.kategori_komponen')
            ->pluck('max_km', 'kategori_komponen')
            ->toArray();

        // 3. Scan KSB thresholds at projected KM
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

        // 4. Map critical components to dynamic AI complaints for more realistic next-service predictions
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

        $prediksi['rekomendasi'] = $rekomendasi;
        $prediksi['description'] = CARTPredictor::getRekomendasiDescription($rekomendasi);
        $prediksi['motor'] = [
            'no_polisi' => $motor->no_polisi,
            'merk' => $motor->merk,
            'tipe_motor' => $motor->tipe_motor,
            'tahun_pembuatan' => $motor->tahun_pembuatan,
            'usia_motor' => $usiaMotor,
        ];
        $prediksi['servis_terakhir'] = [
            'tanggal' => $tanggalServisTerakhir,
            'durasi_hari' => $durasiHari,
        ];
        $prediksi['input_projected'] = [
            'durasi_hari' => $projectedDurasi,
            'km_tempuh' => $projectedKm,
            'usia_motor' => $usiaMotor,
            'kategori_keluhan' => $projectedKeluhan,
            'tipe_motor' => $motor->tipe_motor,
        ];

        return response()->json([
            'success' => true,
            'data' => $prediksi
        ]);
    }

    public function getLastService(Motor $motor)
    {
        $lastServis = TransaksiServis::where('id_motor', $motor->id)
            ->latest('tanggal_servis')
            ->first();

        if (!$lastServis) {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Belum ada riwayat servis untuk motor ini'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tanggal_servis' => $lastServis->tanggal_servis->format('Y-m-d'),
                'km_saat_ini' => $lastServis->km_saat_ini,
                'kategori_keluhan' => $lastServis->kategori_keluhan,
                'rekomendasi_servis' => $lastServis->rekomendasi_servis,
                'tindakan_mekanik' => $lastServis->tindakan_mekanik,
            ]
        ]);
    }

    public function testPage()
    {
        $categories = $this->predictor->getCategories();
        $kategoriKeluhan = $categories['keluhan'];
        $tipeMotor = $categories['tipe'];
        $rekomendasiOptions = $categories['rekomendasi'];

        $apiStatus = $this->predictor->isApiAvailable();
        $modelInfo = $apiStatus ? $this->predictor->getModelInfo() : null;

        return view('prediksi.test', compact(
            'kategoriKeluhan',
            'tipeMotor',
            'rekomendasiOptions',
            'apiStatus',
            'modelInfo'
        ));
    }
    public function testPredict(Request $request)
    {
        $validated = $request->validate([
            'durasi_hari' => 'required|integer|min:0',
            'km_tempuh' => 'required|integer|min:0',
            'usia_motor' => 'required|integer|min:0',
            'kategori_keluhan' => 'required|string',
            'tipe_motor' => 'required|string',
        ]);

        $prediksi = $this->predictor->predict(
            $validated['durasi_hari'],
            $validated['km_tempuh'],
            $validated['usia_motor'],
            $validated['kategori_keluhan'],
            $validated['tipe_motor']
        );

        $prediksi['description'] = CARTPredictor::getRekomendasiDescription($prediksi['rekomendasi']);

        return response()->json([
            'success' => true,
            'data' => $prediksi
        ]);
    }
    public function apiStatus()
    {
        $isAvailable = $this->predictor->isApiAvailable();
        $modelInfo = $isAvailable ? $this->predictor->getModelInfo() : null;

        return response()->json([
            'success' => true,
            'api_available' => $isAvailable,
            'model_info' => $modelInfo
        ]);
    }
}
