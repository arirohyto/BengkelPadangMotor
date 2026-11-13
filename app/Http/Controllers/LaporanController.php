<?php

namespace App\Http\Controllers;

use App\Models\TransaksiServis;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class LaporanController extends Controller
{
    private const BULAN_INDO = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    private const BULAN_SHORT_INDO = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agt',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    public function index(Request $request): View
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $yearExpression = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite' 
            ? "strftime('%Y', tanggal_servis)" 
            : "YEAR(tanggal_servis)";

        $availableYears = TransaksiServis::selectRaw("$yearExpression as tahun")
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([$tahun]);
        }

        // Monthly recap
        $rekapBulanan = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $queryAll = TransaksiServis::whereYear('tanggal_servis', $tahun)
                ->whereMonth('tanggal_servis', $bulan);

            $querySelesai = clone $queryAll;
            $querySelesai->where('status', 'selesai');

            $rekapBulanan[] = [
                'bulan' => self::BULAN_INDO[$bulan],
                'bulan_num' => $bulan,
                'jumlah_transaksi' => $queryAll->count(),
                'total_pendapatan' => (float)$querySelesai->sum('total_biaya'),
            ];
        }

        // Summary
        $totalPendapatanTahun = collect($rekapBulanan)->sum('total_pendapatan');
        $bulanAktif = collect($rekapBulanan)->filter(fn($b) => $b['total_pendapatan'] > 0);
        $rataRataBulanan = $bulanAktif->count() > 0 ? $totalPendapatanTahun / $bulanAktif->count() : 0;
        $bulanTertinggi = $bulanAktif->sortByDesc('total_pendapatan')->first();
        $totalTransaksiTahun = collect($rekapBulanan)->sum('jumlah_transaksi');

        return view('laporan.index', compact(
            'tahun',
            'availableYears',
            'rekapBulanan',
            'totalPendapatanTahun',
            'rataRataBulanan',
            'bulanTertinggi',
            'totalTransaksiTahun'
        ));
    }

    public function detail($tahun, $bulan): View
    {
        $transactions = TransaksiServis::with(['pelanggan', 'motor', 'kasir'])
            ->whereYear('tanggal_servis', $tahun)
            ->whereMonth('tanggal_servis', $bulan)
            ->orderBy('tanggal_servis', 'asc')
            ->get();

        $namaBulan = self::BULAN_INDO[(int)$bulan];
        $totalPendapatan = $transactions->where('status', 'selesai')->sum('total_biaya');

        return view('laporan.detail', compact('transactions', 'tahun', 'bulan', 'namaBulan', 'totalPendapatan'));
    }

    public function chartData(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);

        $data = [];
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $total = TransaksiServis::whereYear('tanggal_servis', $tahun)
                ->whereMonth('tanggal_servis', $bulan)
                ->where('status', 'selesai')
                ->sum('total_biaya');

            $data[] = [
                'bulan' => self::BULAN_SHORT_INDO[$bulan],
                'total' => (float)$total,
            ];
        }

        return response()->json($data);
    }
}
