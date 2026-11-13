<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Sparepart;
use App\Models\Motor;
use App\Models\TransaksiServis;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function calculateTrend($current, $previous) {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    public function index(): View
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Total Pelanggan Trend
        $totalPelanggan = Pelanggan::count();
        $pelangganLastMonth = Pelanggan::where('created_at', '<', $startOfMonth)->count();
        $trendPelanggan = $this->calculateTrend($totalPelanggan, $pelangganLastMonth);

        // 2. Servis Aktif (Hari ini vs Kemarin)
        $transaksiHariIni = TransaksiServis::whereDate('tanggal_servis', $today)->count();
        $transaksiKemarin = TransaksiServis::whereDate('tanggal_servis', $yesterday)->count();
        $trendServis = $this->calculateTrend($transaksiHariIni, $transaksiKemarin);

        // 3. Pendapatan Hari Ini (vs Kemarin)
        $pendapatanHariIni = TransaksiServis::whereDate('tanggal_servis', $today)
            ->where('status', 'selesai')
            ->sum('total_biaya');
        $pendapatanKemarin = TransaksiServis::whereDate('tanggal_servis', $yesterday)
            ->where('status', 'selesai')
            ->sum('total_biaya');
        $trendPendapatan = $this->calculateTrend($pendapatanHariIni, $pendapatanKemarin);

        $stats = [
            'total_pelanggan' => $totalPelanggan,
            'trend_pelanggan' => round($trendPelanggan, 1),
            'transaksi_hari_ini' => $transaksiHariIni,
            'trend_servis' => round($trendServis, 1),
            'pendapatan_hari_ini' => $pendapatanHariIni,
            'trend_pendapatan' => round($trendPendapatan, 1),
            'stok_menipis' => Sparepart::where('stok', '<=', 5)->count(),
        ];

        $recentTransaksi = TransaksiServis::with(['pelanggan', 'motor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $prediksiStats = TransaksiServis::whereMonth('tanggal_servis', Carbon::now()->month)
            ->whereYear('tanggal_servis', Carbon::now()->year)
            ->selectRaw('rekomendasi_servis, COUNT(*) as total')
            ->groupBy('rekomendasi_servis')
            ->pluck('total', 'rekomendasi_servis')
            ->toArray();

        return view('dashboard', compact(
            'stats',
            'recentTransaksi',
            'prediksiStats'
        ));
    }

    public function getPendapatanChart()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = TransaksiServis::whereDate('tanggal_servis', $date)
                ->where('status', 'selesai')
                ->sum('total_biaya');
                
            $data[] = [
                'tanggal' => $date->format('d M'),
                'total' => (float)$total,
            ];
        }
        
        return response()->json($data);
    }
}
