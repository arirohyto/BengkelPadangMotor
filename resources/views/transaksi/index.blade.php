@extends('layouts.app')

@section('title', 'Riwayat Servis')

@section('breadcrumb')
    <li class="breadcrumb-item active">Riwayat Servis</li>
@endsection

@push('styles')
<style>
    .table-b2b {
        border-collapse: collapse;
        width: 100%;
    }
    .table-b2b th {
        padding: 1rem 1.25rem;
        background-color: #f9fafb;
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.05em;
        font-weight: 600;
        border-bottom: 1px solid #e5e7eb;
    }
    .table-b2b td {
        padding: 1.25rem; 
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6; 
    }
    .table-b2b tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-b2b tbody tr:hover {
        background-color: #f9fafb;
    }
    .tabular-nums {
        font-variant-numeric: tabular-nums; 
        letter-spacing: -0.01em;
    }
    
    .badge-semantic {
        padding: 0.4rem 0.85rem;
        border-radius: 9999px; 
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        white-space: nowrap;
    }
    .badge-status-warning { 
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fef3c7;
    }
    .badge-status-info { 
        background-color: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
    }
    .badge-status-success { 
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #dcfce3;
    }
    .badge-status-danger { 
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fee2e2;
    }

    .btn-touch {
        min-width: 44px; 
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-touch:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .empty-state {
        padding: 5rem 2rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #9ca3af;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Riwayat Servis</h4>
            <p class="text-muted mb-0">Manajemen antrean dan histori transaksi servis bengkel</p>
        </div>
        <a href="{{ route('transaksi.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" style="height: 44px; padding: 0 1.5rem; font-weight: 500; border-radius: 8px;">
            <i class="bi bi-plus-lg"></i> Daftarkan Servis Baru
        </a>
    </div>

    <!-- Filter -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('transaksi.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-2">Cari Kata Kunci</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Pelanggan atau plat nomor..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small fw-semibold mb-2">Status Servis</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach($statusOptions as $s)
                                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small fw-semibold mb-2">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small fw-semibold mb-2">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="w-100 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm" style="height: 44px; font-weight: 500;">
                                Filter
                            </button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-light border w-100 shadow-sm text-secondary" style="height: 44px; display: inline-flex; align-items: center; justify-content: center; font-weight: 500;">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-b2b mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No. Ref</th>
                        <th>Tanggal Masuk</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Keluhan</th>
                        <th class="text-end">Total Biaya</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4" width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $trx)
                        <tr>
                            <td class="ps-4">
                                <a href="{{ route('transaksi.show', $trx) }}" class="text-decoration-none fw-bold" style="color: #4f46e5;">
                                    #{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="tabular-nums text-muted">{{ $trx->tanggal_servis->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('pelanggan.show', $trx->pelanggan) }}" class="text-decoration-none text-dark fw-bold">
                                    {{ $trx->pelanggan->nama }}
                                </a>
                                <div class="small text-muted mt-1">{{ $trx->pelanggan->no_hp ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border tabular-nums fw-semibold p-2">
                                    <i class="bi bi-bicycle me-1 text-muted"></i> {{ $trx->motor->no_polisi }}
                                </span>
                            </td>
                            <td class="text-muted">
                                {{ $trx->kategori_keluhan }}
                            </td>
                            <td class="text-end fw-bold tabular-nums">
                                {{ $trx->total_biaya_formatted }}
                            </td>
                            <td class="text-center">
                                @php
                                    $icon = match($trx->status) {
                                        'diterima' => 'bi-hourglass-split',
                                        'dikerjakan' => 'bi-tools',
                                        'selesai' => 'bi-check2-all',
                                        default => 'bi-record-circle'
                                    };
                                @endphp
                                <span class="badge-semantic badge-status-{{ $trx->status_badge }}">
                                    <i class="bi {{ $icon }}"></i> {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('transaksi.show', $trx) }}" class="btn btn-light btn-touch border" title="Lihat Detail">
                                        <i class="bi bi-eye text-primary fs-5"></i>
                                    </a>
                                    <a href="{{ route('transaksi.edit', $trx) }}" class="btn btn-light btn-touch border" title="Edit Transaksi">
                                        <i class="bi bi-pencil text-warning fs-5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border-0">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-inboxes"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2 text-dark">Belum Ada Riwayat Servis</h5>
                                    <p class="text-muted mb-4" style="max-width: 420px; line-height: 1.6;">
                                        @if(request()->anyFilled(['search', 'status', 'dari_tanggal', 'sampai_tanggal']))
                                            Tidak ditemukan transaksi servis yang sesuai dengan kata kunci atau filter status yang Anda gunakan.
                                        @else
                                            Sistem belum mencatat adanya transaksi servis. Mulai kelola bengkel dengan menambahkan antrean kendaraan masuk hari ini.
                                        @endif
                                    </p>
                                    @if(request()->anyFilled(['search', 'status', 'dari_tanggal', 'sampai_tanggal']))
                                        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary px-4 shadow-sm" style="height: 44px; display: inline-flex; align-items: center;">
                                            <i class="bi bi-arrow-counterclockwise me-2"></i> Reset Filter
                                        </a>
                                    @else
                                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary px-4 shadow-sm" style="height: 44px; display: inline-flex; align-items: center; font-weight: 500;">
                                            <i class="bi bi-plus-lg me-2"></i> Daftarkan Servis Baru
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(!request('status') || request('status') === 'selesai')
                    @if($transaksi->isNotEmpty())
                        <tfoot>
                            <tr class="table-light fw-bold" style="border-top: 2px solid #e5e7eb;">
                                <td colspan="5" class="text-end py-3 text-secondary">Total Pendapatan:</td>
                                <td class="text-end py-3 fs-6" style="color: #15803d;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                @endif
            </table>
        </div>
        @if($transaksi->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $transaksi->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection