@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan Keuangan</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Laporan Keuangan</h4>
            <p class="text-muted mb-0">Rekap pendapatan Bengkel Padang Motor</p>
        </div>
        <form action="{{ route('laporan.index') }}" method="GET" class="d-flex gap-2 align-items-center">
            <label class="text-muted small mb-0 me-1">Tahun:</label>
            <select name="tahun" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">Total Pendapatan {{ $tahun }}</div>
                <div class="stat-value">Rp {{ number_format($totalPendapatanTahun, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-label">Rata-rata per Bulan</div>
                <div class="stat-value">Rp {{ number_format($rataRataBulanan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-label">Total Transaksi {{ $tahun }}</div>
                <div class="stat-value">{{ number_format($totalTransaksiTahun) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-icon"><i class="bi bi-trophy"></i></div>
                <div class="stat-label">Bulan Tertinggi</div>
                <div class="stat-value" style="font-size: 1.25rem;">
                    @if($bulanTertinggi)
                        {{ $bulanTertinggi['bulan'] }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-bar-chart-line me-2"></i>Grafik Pendapatan Bulanan {{ $tahun }}
        </div>
        <div class="card-body">
            <canvas id="chartPendapatan" height="100"></canvas>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-table me-2"></i>Rekap Pendapatan per Bulan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                 <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-end">Total Pendapatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapBulanan as $i => $rekap)
                            <tr class="{{ $rekap['jumlah_transaksi'] > 0 ? '' : 'text-muted' }}">
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $rekap['bulan'] }}</td>
                                <td class="text-center">{{ $rekap['jumlah_transaksi'] }}</td>
                                <td class="text-end">
                                    @if($rekap['total_pendapatan'] > 0)
                                        <span class="fw-medium">Rp {{ number_format($rekap['total_pendapatan'], 0, ',', '.') }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rekap['jumlah_transaksi'] > 0)
                                        <a href="{{ route('laporan.detail', ['tahun' => $tahun, 'bulan' => $rekap['bulan_num']]) }}" class="btn btn-sm btn-outline-primary py-0">
                                            <i class="bi bi-eye me-1"></i> Lihat Nota
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">TOTAL</td>
                            <td class="text-center">{{ $totalTransaksiTahun }}</td>
                            <td class="text-end text-primary">Rp {{ number_format($totalPendapatanTahun, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const labels = @json(collect($rekapBulanan)->pluck('bulan'));
    const values = @json(collect($rekapBulanan)->pluck('total_pendapatan'));

    new Chart(document.getElementById('chartPendapatan'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: values,
                backgroundColor: values.map(v => v > 0 ? 'rgba(37, 99, 235, 0.7)' : 'rgba(203, 213, 225, 0.3)'),
                borderColor: values.map(v => v > 0 ? 'rgba(37, 99, 235, 1)' : 'rgba(203, 213, 225, 0.5)'),
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => 'Rp ' + (val / 1000000).toFixed(1) + 'jt'
                    }
                }
            }
        }
    });
</script>
@endpush
