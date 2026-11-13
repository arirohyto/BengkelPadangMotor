@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pelanggan.index') }}">Data Pelanggan</a></li>
    <li class="breadcrumb-item active">{{ $pelanggan->nama }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- Customer Info -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Informasi Pelanggan
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px;">
                            <i class="bi bi-person-fill text-primary fs-1"></i>
                        </div>
                        <h5 class="mt-3 mb-0">{{ $pelanggan->nama }}</h5>
                        <p class="text-muted">ID: #{{ $pelanggan->id }}</p>
                    </div>

                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex">
                                <i class="bi bi-telephone text-muted me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Nomor HP</small>
                                    <a href="tel:{{ $pelanggan->no_hp }}">{{ $pelanggan->no_hp }}</a>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <i class="bi bi-geo-alt text-muted me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Alamat</small>
                                    {{ $pelanggan->alamat ?: 'Tidak ada alamat' }}
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <i class="bi bi-calendar text-muted me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Terdaftar</small>
                                    {{ $pelanggan->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Summary Info -->
                    <div class="row g-2 mt-2 mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center bg-light">
                                <div class="fw-bold text-primary fs-5">
                                    {{ $pelanggan->transaksi->count() }}
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">Total Servis</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center bg-light">
                                <div class="fw-bold text-success fs-5">
                                    Rp {{ number_format($pelanggan->transaksi->sum('total_biaya'), 0, ',', '.') }}
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">Total Pengeluaran</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('pelanggan.edit', $pelanggan) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit Data
                        </a>

                        <form action="{{ route('pelanggan.destroy', $pelanggan) }}" 
                              method="POST" class="mt-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Yakin ingin menghapus pelanggan {{ $pelanggan->nama }}? Data motor dan riwayat servis terkait juga akan terpengaruh.')">
                                <i class="bi bi-trash me-1"></i>Hapus Pelanggan
                            </button>
                        </form>

                        <a href="{{ route('transaksi.create', ['pelanggan_id' => $pelanggan->id]) }}"
                            class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Input Servis
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Motor List -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bicycle me-2"></i>Daftar Motor</span>
                    <a href="{{ route('motor.create', ['pelanggan_id' => $pelanggan->id]) }}"
                        class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Motor
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>No. Polisi</th>
                                    <th>Merk</th>
                                    <th>Tipe</th>
                                    <th>Tahun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggan->motors as $motor)
                                    <tr>
                                        <td class="fw-medium">{{ $motor->no_polisi }}</td>
                                        <td>{{ $motor->merk }}</td>
                                        <td><span class="badge bg-secondary">{{ $motor->tipe_motor }}</span></td>
                                        <td>{{ $motor->tahun_pembuatan }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('motor.show', $motor) }}" 
                                                   class="btn btn-outline-info" 
                                                   title="Lihat Detail Motor">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('transaksi.create', [
                                                            'motor_id' => $motor->id,
                                                            'pelanggan_id' => $pelanggan->id
                                                          ]) }}" 
                                                   class="btn btn-outline-primary" 
                                                   title="Input Servis Baru">
                                                    <i class="bi bi-plus-lg"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            Belum ada motor terdaftar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Service History -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Servis Terakhir
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Motor</th>
                                    <th>Keluhan</th>
                                    <th>Rekomendasi</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th width="100" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggan->transaksiServis as $trx)
                                    <tr>
                                        <td>{{ $trx->tanggal_servis->format('d/m/Y') }}</td>
                                        <td>{{ $trx->motor->no_polisi }}</td>
                                        <td>{{ $trx->kategori_keluhan }}</td>
                                        <td>
                                            <span class="badge bg-{{ $trx->rekomendasi_badge }}">
                                                {{ $trx->rekomendasi_servis }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $trx->status_badge }}">
                                                {{ ucfirst($trx->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $trx->total_biaya_formatted }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('transaksi.show', $trx) }}" 
                                               class="btn btn-outline-info btn-sm"
                                               title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-clock-history fs-2 text-muted d-block mb-2"></i>
                                            <span class="text-muted">Pelanggan ini belum pernah servis</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection