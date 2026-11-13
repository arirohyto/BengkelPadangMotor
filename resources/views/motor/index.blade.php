@extends('layouts.app')

@section('title', 'Data Motor')

@section('breadcrumb')
    <li class="breadcrumb-item active">Data Motor</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Data Motor</h4>
            <p class="text-muted mb-0">Kelola data kendaraan pelanggan</p>
        </div>
        <a href="{{ route('motor.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Tambah Motor
        </a>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('motor.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari no polisi, model motor, atau nama pelanggan..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="tipe" class="form-select">
                            <option value="">Semua Tipe</option>
                            @foreach($tipeOptions as $t)
                                <option value="{{ $t }}" {{ request('tipe') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('motor.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>No. Polisi</th>
                            <th>Model Motor</th>
                            <th>Tipe</th>
                            <th>Tahun</th>
                            <th>KM Terakhir</th>
                            <th>Terakhir Servis</th>
                            <th>Pemilik</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($motors as $index => $m)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $m->no_polisi }}</td>
                                <td>{{ $m->merk }}</td>
                                <td><span class="badge bg-secondary">{{ $m->tipe_motor }}</span></td>
                                <td>{{ $m->tahun_pembuatan }}</td>
                                <td>
                                    @if($m->transaksiTerakhir)
                                        {{ number_format($m->transaksiTerakhir->km_saat_ini, 0, ',', '.') }} KM
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($m->transaksiTerakhir)
                                        {{ \Carbon\Carbon::parse($m->transaksiTerakhir->tanggal_servis)->format('d/m/Y') }}
                                    @else
                                        <span class="badge bg-secondary text-white" style="background-color: #6c757d !important;">Belum Servis</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pelanggan.show', $m->pelanggan) }}" class="text-decoration-none">
                                        {{ $m->pelanggan->nama }}
                                    </a>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('motor.show', $m) }}"
                                            class="btn btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('motor.edit', $m) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('motor.destroy', $m) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus motor ini? Riwayat servis terkait juga akan terpengaruh.')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data motor
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($motors->hasPages())
            <div class="card-footer">
                {{ $motors->links() }}
            </div>
        @endif
    </div>
@endsection