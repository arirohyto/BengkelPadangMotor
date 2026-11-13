@extends('layouts.app')

@section('title', 'Detail Laporan Keuangan - ' . $namaBulan . ' ' . $tahun)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('laporan.index', ['tahun' => $tahun]) }}">Laporan Keuangan</a></li>
    <li class="breadcrumb-item active">Detail {{ $namaBulan }} {{ $tahun }}</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Detail Transaksi - {{ $namaBulan }} {{ $tahun }}</h4>
            <p class="text-muted mb-0">Daftar nota transaksi selesai pada bulan ini</p>
        </div>
        <a href="{{ route('laporan.index', ['tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Laporan
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">Total Pendapatan {{ $namaBulan }} {{ $tahun }}</div>
                <div class="stat-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card info">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-label">Total Transaksi Selesai</div>
                <div class="stat-value">{{ number_format($transactions->count()) }}</div>
            </div>
        </div>
    </div>

    <!-- Transaction List Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-list-stars me-2"></i>Rincian Transaksi</span>
            <span class="badge bg-primary">{{ $transactions->count() }} Transaksi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 5%;">No</th>
                            <th style="width: 13%;">Hari & Tanggal</th>
                            <th style="width: 10%;">No. Nota</th>
                            <th style="width: 15%;">Pelanggan</th>
                            <th style="width: 15%;">Motor</th>
                            <th style="width: 12%;">Jenis Servis</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Kasir</th>
                            <th class="text-end pe-3" style="width: 12%;">Total Biaya</th>
                            <th class="text-center" style="width: 8%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $i => $t)
                            <tr>
                                <td class="ps-3">{{ $i + 1 }}</td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $t->tanggal_servis->translatedFormat('l') }}</span>
                                    <span class="text-muted small">{{ $t->tanggal_servis->translatedFormat('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary font-monospace" style="font-size: 0.85rem;">#TRX-{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $t->pelanggan->nama }}</div>
                                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $t->pelanggan->no_hp ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $t->motor->merk }} ({{ $t->motor->tipe_motor }})</div>
                                    <div class="text-muted small font-monospace"><i class="bi bi-bicycle me-1"></i>{{ $t->motor->no_polisi }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-white" style="font-size: 0.8rem;">{{ $t->jenis_servis }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $t->status_badge }}">{{ ucfirst($t->status) }}</span>
                                </td>
                                <td>
                                    <span class="text-dark small"><i class="bi bi-person me-1"></i>{{ $t->kasir ? $t->kasir->name : '-' }}</span>
                                </td>
                                <td class="text-end pe-3 fw-bold {{ $t->status === 'selesai' ? 'text-success' : 'text-muted' }}" style="font-size: 1rem;">
                                    Rp {{ number_format($t->total_biaya, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('transaksi.show', $t->id) }}" class="btn btn-xs btn-outline-primary" title="Detail Transaksi">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        @if($t->status === 'selesai')
                                            <a href="{{ route('transaksi.nota', $t->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Cetak Nota">
                                                <i class="bi bi-printer"></i> Nota
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-x fs-1 mb-2 d-block"></i>
                                    Tidak ada transaksi untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="8" class="text-end text-uppercase">Total Pendapatan Bulan ini</td>
                            <td class="text-end pe-3 text-primary" style="font-size: 1.15rem;">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
