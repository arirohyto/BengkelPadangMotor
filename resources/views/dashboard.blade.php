@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .page-title { font-weight: 700; color: #1e293b; letter-spacing: -0.025em; font-size: 1.75rem; }
    .page-subtitle { color: #64748b; font-size: 0.875rem; }
    
    .card-custom {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .card-header-custom {
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        background: #ffffff;
    }
    
    .card-title-custom {
        font-weight: 600;
        color: #1e293b;
        font-size: 1.1rem;
        margin: 0;
    }

    /* Metric Cards */
    .metric-card {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .metric-icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        width: 44px;
        height: 44px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary-color) !important;
        box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.15), 0 0 0 3px rgba(234, 88, 12, 0.05);
    }

    .metric-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .metric-trend {
        font-size: 0.75rem;
        color: #64748b;
    }
    .trend-up { color: #16a34a; font-weight: 600; }
    .trend-down { color: #dc2626; font-weight: 600; }
    .trend-alert { color: var(--primary-color); font-weight: 600; }

    /* Table Custom */
    .table-clean { border-collapse: collapse; width: 100%; }
    .table-clean th {
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-clean td {
        padding: 1rem;
        font-size: 0.85rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    
    .badge-soft-success { background: #dcfce3; color: #16a34a; padding: 0.35rem 0.75rem; border-radius: 999px; font-weight: 600; font-size: 0.75rem; }
    .badge-soft-warning { background: #fef3c7; color: #d97706; padding: 0.35rem 0.75rem; border-radius: 999px; font-weight: 600; font-size: 0.75rem; }

    /* Button Action Hover Styles */
    .btn-action-edit {
        background: transparent;
        color: var(--primary-color);
        border: 1px solid transparent;
        border-radius: 6px;
        padding: 0.35rem 0.6rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-action-edit:hover {
        background: var(--primary-color);
        color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.2);
    }

    /* Main CTA Button Styles */
    .btn-action-input {
        background: transparent;
        color: var(--primary-color);
        border: 1.5px solid var(--primary-color);
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-action-input i {
        color: var(--primary-color);
        transition: color 0.3s ease;
    }
    .btn-action-input:hover {
        background: #1e293b;
        color: #ffffff !important;
        border-color: var(--primary-color);
        box-shadow: 0 0 15px rgba(234, 88, 12, 0.4);
    }
    .btn-action-input:hover i {
        color: #fbbf24; /* yellow-400 */
    }

    /* ML Gauge Styles */
    .ml-item {
        margin-bottom: 1.5rem;
    }
    .ml-title { font-weight: 600; color: #334155; font-size: 0.95rem; margin-bottom: 0.25rem; }
    .ml-desc { font-size: 0.75rem; color: #64748b; font-weight: 600; margin-top: 0.25rem; text-align: right;}
    
    .gauge-wrapper {
        position: relative;
        width: 100px;
        height: 50px;
        overflow: hidden;
        margin-left: auto;
    }
    .gauge-bg {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 10px solid #f1f5f9;
        position: absolute;
        top: 0; left: 0;
        box-sizing: border-box;
    }
    .gauge-fill {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 10px solid var(--primary-color);
        position: absolute;
        top: 0; left: 0;
        box-sizing: border-box;
        border-bottom-color: transparent;
        border-left-color: transparent;
        transform: rotate(45deg); /* Base for 100% */
    }
    .gauge-fill.val-85 { transform: rotate(105deg); }
    .gauge-fill.val-90 { transform: rotate(115deg); }
    
    .gauge-needle {
        position: absolute;
        bottom: -5px;
        left: 48%;
        width: 4px;
        height: 35px;
        background: #1e293b;
        transform-origin: bottom center;
        border-radius: 4px;
    }
    .gauge-needle.val-85 { transform: rotate(50deg); }
    .gauge-needle.val-90 { transform: rotate(65deg); }
    
    .linear-progress {
        height: 10px;
        background: #f1f5f9;
        border-radius: 5px;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .linear-progress-bar {
        height: 100%;
        background: var(--primary-color);
        border-radius: 5px;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
        <div>
            <h1 class="page-title mb-1">Dashboard</h1>
            <p class="page-subtitle mb-0">Sistem Informasi Manajemen Operasional & Prediksi Perawatan Bengkel Padang Motor</p>
        </div>
        <a href="{{ route('transaksi.create') }}" class="btn-action-input shadow-sm">
            <i class="bi bi-pencil-square fs-5"></i>
            <span>Input Servis Baru</span>
        </a>
    </div>

    <!-- Top Cards -->
    <div class="row g-4 mb-4">
        <!-- Card 1 -->
        <div class="col-md-6 col-xl-3">
            <div class="card-custom metric-card">
                <div class="metric-icon"><i class="bi bi-people"></i></div>
                <div>
                    <div class="metric-title">Total Pelanggan</div>
                    <div class="metric-value">{{ number_format($stats['total_pelanggan']) }}</div>
                    <div class="metric-trend">
                        @if($stats['trend_pelanggan'] > 0)
                            <span class="trend-up"><i class="bi bi-arrow-up-short"></i>{{ $stats['trend_pelanggan'] }}%</span>
                        @elseif($stats['trend_pelanggan'] < 0)
                            <span class="trend-down"><i class="bi bi-arrow-down-short"></i>{{ abs($stats['trend_pelanggan']) }}%</span>
                        @else
                            <span class="text-muted"><i class="bi bi-dash"></i>0%</span>
                        @endif
                         dari bulan lalu
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-md-6 col-xl-3">
            <div class="card-custom metric-card">
                <div class="metric-icon"><i class="bi bi-wrench"></i></div>
                <div>
                    <div class="metric-title">Servis Aktif</div>
                    <div class="metric-value">{{ number_format($stats['transaksi_hari_ini']) }}</div>
                    <div class="metric-trend">
                        @if($stats['trend_servis'] > 0)
                            <span class="trend-up"><i class="bi bi-arrow-up-short"></i>{{ $stats['trend_servis'] }}%</span>
                        @elseif($stats['trend_servis'] < 0)
                            <span class="trend-down"><i class="bi bi-arrow-down-short"></i>{{ abs($stats['trend_servis']) }}%</span>
                        @else
                            <span class="text-muted"><i class="bi bi-dash"></i>0%</span>
                        @endif
                         dari kemarin
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-md-6 col-xl-3">
            <div class="card-custom metric-card">
                <div class="metric-icon"><i class="bi bi-cash"></i></div>
                <div>
                    <div class="metric-title">Pendapatan Hari Ini</div>
                    @php
                        $income = $stats['pendapatan_hari_ini'];
                        if ($income >= 1000000) {
                            $formattedIncome = 'Rp' . number_format($income / 1000000, 1, ',', '.');
                            $formattedIncome = str_replace(',0', '', $formattedIncome) . ' Jt';
                        } elseif ($income >= 1000) {
                            $formattedIncome = 'Rp' . number_format($income / 1000, 0, ',', '.') . ' Rb';
                        } else {
                            $formattedIncome = 'Rp' . number_format($income, 0, ',', '.');
                        }
                    @endphp
                    <div class="metric-value" style="font-size: 1.75rem;">{{ $formattedIncome }}</div>
                    <div class="metric-trend">
                        @if($stats['trend_pendapatan'] > 0)
                            <span class="trend-up"><i class="bi bi-arrow-up-short"></i>{{ $stats['trend_pendapatan'] }}%</span>
                        @elseif($stats['trend_pendapatan'] < 0)
                            <span class="trend-down"><i class="bi bi-arrow-down-short"></i>{{ abs($stats['trend_pendapatan']) }}%</span>
                        @else
                            <span class="text-muted"><i class="bi bi-dash"></i>0%</span>
                        @endif
                         dari kemarin
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="col-md-6 col-xl-3">
            <div class="card-custom metric-card">
                <div class="metric-icon" style="background: #fff7ed; color: #ea580c;"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="metric-title">Stok Kritis</div>
                    <div class="metric-value" style="color: #ea580c;">{{ number_format($stats['stok_menipis']) }}</div>
                    <div class="metric-trend"><a href="{{ route('sparepart.index') }}" class="trend-alert text-decoration-none">Cek detail stok</a></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card-custom mb-4">
        <div class="card-header-custom">
            <i class="bi bi-graph-up-arrow text-primary fs-4 me-3"></i>
            <h3 class="card-title-custom">Tren Pendapatan 7 Hari Terakhir</h3>
        </div>
        <div class="p-4" style="height: 300px; position: relative;">
            <canvas id="pendapatanChart"></canvas>
        </div>
    </div>

    <div class="row g-4 pb-5">
        <!-- Table -->
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="bi bi-clock-history text-secondary fs-4 me-3"></i>
                    <h3 class="card-title-custom">Transaksi Terakhir</h3>
                </div>
                <div class="table-responsive">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Motor</th>
                                <th>Rekomendasi</th>
                                <th>Total Biaya</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransaksi as $trx)
                                <tr>
                                    <td class="ps-4" style="color: #64748b;">{{ $trx->tanggal_servis->format('d M Y') }}</td>
                                    <td style="font-weight: 500;">
                                        @if(str_contains(strtolower($trx->pelanggan->nama), 'kasir'))
                                            Ari (Kasir)
                                        @else
                                            {{ $trx->pelanggan->nama }}
                                        @endif
                                    </td>
                                     <td style="font-weight: 500;">
                                        <div>{{ $trx->motor->merk }}</div>
                                        <div class="text-muted small fw-normal mt-1">{{ $trx->motor->no_polisi }}</div>
                                    </td>
                                    <td style="color: #64748b;">{{ $trx->rekomendasi_servis }}</td>
                                    <td style="font-variant-numeric: tabular-nums;">Rp{{ number_format($trx->total_biaya, 0, ',', '.') }}</td>
                                    <td>
                                        @if($trx->status == 'selesai')
                                            <span class="badge-soft-success">Selesai</span>
                                        @elseif($trx->status == 'dikerjakan')
                                            <span class="badge-soft-warning">Proses</span>
                                        @else
                                            <span class="badge-soft-warning">Diterima</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <a href="{{ route('transaksi.edit', $trx) }}" class="btn-action-edit" title="Edit Transaksi">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi terakhir</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ML Prediction -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="bi bi-pie-chart text-primary fs-4 me-3"></i>
                    <h3 class="card-title-custom">Prediksi Perawatan Bulan Ini</h3>
                </div>
                <div class="p-4">
                    @php
                        $maxTotal = count($prediksiStats) > 0 ? max($prediksiStats) : 1;
                    @endphp
                    
                    @forelse($prediksiStats as $rekomendasi => $total)
                        @php
                            $percentage = ($total / $maxTotal) * 100;
                            // Cyan color exactly like the provided mock image
                            $barColor = '#06b6d4'; 
                        @endphp
                        <div class="ml-item mb-4 pb-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="ml-title mb-0" style="font-size: 0.95rem; font-weight: 500; color: #1e293b;">{{ $rekomendasi }}</div>
                                <span class="badge bg-secondary rounded-3 px-2 py-1 shadow-sm" style="font-size: 0.85rem; background-color: #64748b !important;">{{ $total }}</span>
                            </div>
                            <div class="linear-progress" style="height: 10px; background: #e2e8f0; border-radius: 5px; margin-top: 0.25rem;">
                                <div class="linear-progress-bar" style="width: {{ $percentage }}%; background: {{ $barColor }}; border-radius: 5px;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <p class="mb-0 small">Belum ada data prediksi masuk</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch("{{ route('dashboard.pendapatan-chart') }}")
                .then(res => res.json())
                .then(data => {
                    const labels = data.map(item => item.tanggal);
                    const totals = data.map(item => item.total);
                    
                    const ctx = document.getElementById('pendapatanChart').getContext('2d');
                    
                    // Create gradient for the line chart fill
                    let gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(234, 88, 12, 0.25)');
                    gradient.addColorStop(1, 'rgba(234, 88, 12, 0.0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Pendapatan',
                                data: totals,
                                borderColor: '#ea580c',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ea580c',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1e293b',
                                    padding: 10,
                                    displayColors: false,
                                    callbacks: {
                                        title: function(context) { 
                                            // Format like "14 Jul 2026: Rp2,4 Jt"
                                            let val = context[0].raw;
                                            let formattedVal = val >= 1000000 
                                                ? (val / 1000000).toFixed(1).replace('.', ',').replace(',0', '') + ' Jt' 
                                                : (val / 1000).toFixed(0) + ' Rb';
                                            return context[0].label + ' 2026: Rp' + formattedVal; 
                                        },
                                        label: function() { return null; }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#64748b', font: { size: 11, family: 'Inter' } }
                                },
                                y: {
                                    grid: { color: '#f1f5f9', borderDash: [5, 5] },
                                    border: { display: false },
                                    ticks: {
                                        color: '#64748b',
                                        font: { size: 11, family: 'Inter' },
                                        callback: function(value) {
                                            if(value === 0) return '0';
                                            if(value >= 1000000) {
                                                return 'Rp ' + (value / 1000000).toFixed(1).replace('.', ',').replace(',0', '') + ' Jt';
                                            } else if(value >= 1000) {
                                                return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                            }
                                            return 'Rp ' + value;
                                        },
                                        maxTicksLimit: 6,
                                        padding: 10
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(err => console.error('Gagal memuat grafik pendapatan:', err));
        });
    </script>
@endpush