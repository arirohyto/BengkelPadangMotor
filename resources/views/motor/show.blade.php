@extends('layouts.app')

@section('title', 'Detail Motor - ' . $motor->no_polisi)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('motor.index') }}">Data Motor</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Detail Motor</h4>
            <p class="text-muted mb-0">Rincian data kendaraan dan pemilik</p>
        </div>
        <a href="{{ route('motor.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <!-- Card Informasi Motor -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-bicycle me-2"></i>Informasi Kendaraan
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Spesifikasi Motor</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150" class="text-muted py-2">No. Polisi</td>
                            <td class="fw-semibold py-2 text-uppercase">{{ $motor->no_polisi }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Model Motor</td>
                            <td class="py-2">{{ $motor->merk }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Tipe</td>
                            <td class="py-2">
                                <span class="badge bg-secondary">{{ $motor->tipe_motor }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Tahun Pembuatan</td>
                            <td class="py-2">{{ $motor->tahun_pembuatan }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Pemilik & Riwayat KM</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150" class="text-muted py-2">Nama Pemilik</td>
                            <td class="py-2 fw-semibold">
                                <a href="{{ route('pelanggan.show', $motor->pelanggan) }}" class="text-decoration-none">
                                    {{ $motor->pelanggan->nama }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">No. HP Pemilik</td>
                            <td class="py-2">
                                <a href="tel:{{ $motor->pelanggan->no_hp }}" class="text-decoration-none">
                                    {{ $motor->pelanggan->no_hp }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">KM Terakhir</td>
                            <td class="py-2">
                                @if($motor->transaksiTerakhir)
                                    {{ number_format($motor->transaksiTerakhir->km_saat_ini, 0, ',', '.') }} KM
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Terakhir Servis</td>
                            <td class="py-2">
                                @if($motor->transaksiTerakhir)
                                    {{ \Carbon\Carbon::parse($motor->transaksiTerakhir->tanggal_servis)->format('d/m/Y') }}
                                @else
                                    <span class="text-muted fst-italic">Belum pernah servis</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Riwayat Servis Motor -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-clock-history me-2"></i>Riwayat Servis Motor Ini
        </div>
        <div class="card-body p-0">
            @if($motor->transaksiServis->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keluhan</th>
                                <th>Jenis Servis</th>
                                <th>KM</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($motor->transaksiServis as $trx)
                                <tr>
                                    <td>{{ $trx->tanggal_servis->format('d/m/Y') }}</td>
                                    <td>{{ $trx->kategori_keluhan }}</td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                                            {{ $trx->jenis_servis ?: 'Servis Rutin' }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($trx->km_saat_ini, 0, ',', '.') }} KM</td>
                                    <td>{{ $trx->total_biaya_formatted }}</td>
                                    <td>
                                        <span class="badge bg-{{ $trx->status_badge }}">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('transaksi.show', $trx) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                    Belum ada riwayat servis untuk motor ini
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('motor.edit', $motor) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>Edit Data
                </a>

                <form action="{{ route('motor.destroy', $motor) }}" method="POST" class="d-inline ms-auto"
                    onsubmit="return confirm('Yakin ingin menghapus motor ini? Riwayat servis terkait juga akan terpengaruh.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
