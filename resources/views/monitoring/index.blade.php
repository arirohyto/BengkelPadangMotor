@extends('layouts.app')

@section('title', 'Monitoring Model CART')

@section('breadcrumb')
    <li class="breadcrumb-item active">Monitoring Model CART</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Monitoring Model CART</h4>
            <p class="text-muted mb-0">Status koneksi dan performa model machine learning</p>
        </div>
        <a href="{{ route('monitoring.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </a>
    </div>

    <div class="row g-4">
        <!-- API Status -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-hdd-network me-2"></i>Status Koneksi Flask API
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px;
                                background: {{ $apiAvailable ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }};">
                            <i class="bi {{ $apiAvailable ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }}"
                                style="font-size: 1.75rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 {{ $apiAvailable ? 'text-success' : 'text-danger' }}">
                                {{ $apiAvailable ? 'Online' : 'Offline' }}
                            </h5>
                            <small class="text-muted">
                                {{ $apiAvailable ? 'Flask API terhubung dan siap menerima prediksi' : 'Flask API tidak dapat dijangkau. Pastikan server berjalan.' }}
                            </small>
                        </div>
                    </div>

                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="140">Endpoint</td>
                            <td><code>http://127.0.0.1:5000</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terakhir Dicek</td>
                            <td>{{ now()->format('d M Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Prediksi</td>
                            <td><strong>{{ number_format($totalPrediksi) }}</strong> transaksi</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Model Info -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <i class="bi bi-cpu me-2"></i>Informasi Model CART
                </div>
                <div class="card-body">
                    @if($modelInfo)
                        <table class="table table-sm table-borderless mb-0">
                            @if(isset($modelInfo['best_params']))
                                <tr>
                                    <td class="text-muted" width="160">Max Depth</td>
                                    <td><span class="badge bg-primary">{{ $modelInfo['best_params']['max_depth'] ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Min Samples Split</td>
                                    <td><span class="badge bg-primary">{{ $modelInfo['best_params']['min_samples_split'] ?? '-' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Min Samples Leaf</td>
                                    <td><span class="badge bg-primary">{{ $modelInfo['best_params']['min_samples_leaf'] ?? '-' }}</span></td>
                                </tr>
                            @endif
                            @if(isset($modelInfo['best_f1_score']))
                                <tr>
                                    <td class="text-muted">F1-Score (CV)</td>
                                    <td>
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($modelInfo['best_f1_score'] * 100, 2) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            @if(isset($modelInfo['training_samples']))
                                <tr>
                                    <td class="text-muted">Data Training</td>
                                    <td><strong>{{ $modelInfo['training_samples'] }}</strong> sampel</td>
                                </tr>
                            @endif
                            @if(isset($modelInfo['test_samples']))
                                <tr>
                                    <td class="text-muted">Data Testing</td>
                                    <td><strong>{{ $modelInfo['test_samples'] }}</strong> sampel</td>
                                </tr>
                            @endif
                            @if(isset($modelInfo['features']))
                                <tr>
                                    <td class="text-muted">Fitur Input</td>
                                    <td>
                                        @foreach($modelInfo['features'] as $feat)
                                            <span class="badge bg-light text-dark border me-1">{{ $feat }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        </table>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>
                            <p>Informasi model tidak tersedia.<br>Pastikan Flask API sedang berjalan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prediction Distribution -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pie-chart me-2"></i>Distribusi Rekomendasi Bulan Ini
                </div>
                <div class="card-body">
                    @if(!empty($prediksiStats))
                        <canvas id="chartDistribusi" height="250"></canvas>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                            <p>Belum ada data prediksi bulan ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Prediction Stats Table -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-list-ol me-2"></i>Statistik Rekomendasi Bulan Ini
                </div>
                <div class="card-body p-0">
                    @if(!empty($prediksiStats))
                        @php $totalBulanIni = array_sum($prediksiStats); @endphp
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Rekomendasi</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prediksiStats as $rekomendasi => $jumlah)
                                    <tr>
                                        <td>
                                            @php
                                                $badgeColor = match($rekomendasi) {
                                                    'Servis Ringan' => 'success',
                                                    'Servis CVT' => 'info',
                                                    'Servis Besar' => 'warning',
                                                    'Ganti Part Fast Moving' => 'danger',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }}">{{ $rekomendasi }}</span>
                                        </td>
                                        <td class="text-center">{{ $jumlah }}</td>
                                        <td class="text-end">{{ number_format(($jumlah / $totalBulanIni) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-center">{{ $totalBulanIni }}</td>
                                    <td class="text-end">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Belum ada data.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@if(!empty($prediksiStats))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const labels = @json(array_keys($prediksiStats));
    const values = @json(array_values($prediksiStats));
    const colors = labels.map(l => {
        switch(l) {
            case 'Servis Ringan': return '#22c55e';
            case 'Servis CVT': return '#06b6d4';
            case 'Servis Besar': return '#f59e0b';
            case 'Ganti Part Fast Moving': return '#ef4444';
            default: return '#64748b';
        }
    });

    new Chart(document.getElementById('chartDistribusi'), {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true }
                }
            }
        }
    });
</script>
@endif
@endpush
