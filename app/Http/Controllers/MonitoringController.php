<?php

namespace App\Http\Controllers;

use App\Models\TransaksiServis;
use App\Services\CARTPredictor;
use Illuminate\View\View;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    protected CARTPredictor $predictor;

    public function __construct()
    {
        $this->predictor = new CARTPredictor();
    }

    public function index(): View
    {
        $apiAvailable = $this->predictor->isApiAvailable();
        $modelInfo = $apiAvailable ? $this->predictor->getModelInfo() : null;

        $prediksiStats = TransaksiServis::whereMonth('tanggal_servis', Carbon::now()->month)
            ->whereYear('tanggal_servis', Carbon::now()->year)
            ->selectRaw('rekomendasi_servis, COUNT(*) as total')
            ->groupBy('rekomendasi_servis')
            ->pluck('total', 'rekomendasi_servis')
            ->toArray();

        $totalPrediksi = TransaksiServis::count();

        return view('monitoring.index', compact(
            'apiAvailable',
            'modelInfo',
            'prediksiStats',
            'totalPrediksi'
        ));
    }
}
