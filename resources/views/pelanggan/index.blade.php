@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Data Pelanggan</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Data Pelanggan</h4>
            <p class="text-muted mb-0">Kelola data pelanggan bengkel</p>
        </div>
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Tambah Pelanggan
        </a>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('pelanggan.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, no HP, atau alamat..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="jumlah_motor" class="form-select">
                            <option value="">Semua (Jumlah Motor)</option>
                            <option value="1" {{ request('jumlah_motor') == '1' ? 'selected' : '' }}>1 Motor</option>
                            <option value="2" {{ request('jumlah_motor') == '2' ? 'selected' : '' }}>2 Motor</option>
                            <option value="3+" {{ request('jumlah_motor') == '3+' ? 'selected' : '' }}>3+ Motor</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary">
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
                            <th>Nama</th>
                            <th>No. HP</th>
                            <th width="200">Alamat</th>
                            <th>Motor</th>
                            <th>Terakhir Servis</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggan as $index => $p)
                            <tr>
                                <td>{{ $pelanggan->firstItem() + $index }}</td>
                                <td>
                                    <a href="{{ route('pelanggan.show', $p) }}" class="text-decoration-none fw-medium">
                                        {{ $p->nama }}
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:{{ $p->no_hp }}" class="text-decoration-none">
                                        {{ $p->no_hp }}
                                    </a>
                                </td>
                                <td title="{{ $p->alamat ?: 'Tidak ada alamat' }}" style="cursor: help;">
                                    {{ Str::limit($p->alamat, 30) ?: '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $p->motors->count() }} motor</span>
                                </td>
                                <td>
                                    @if($p->transaksi && $p->transaksi->isNotEmpty())
                                        {{ \Carbon\Carbon::parse($p->transaksi->first()->tanggal_servis)->format('d/m/Y') }}
                                    @else
                                        <span class="badge bg-secondary text-white" style="background-color: #6c757d !important;">Belum Servis</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('pelanggan.show', $p) }}" class="btn btn-outline-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('pelanggan.edit', $p) }}" class="btn btn-outline-warning"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                        <form action="{{ route('pelanggan.destroy', $p) }}" method="POST"
                                            class="d-inline ms-1"
                                            onsubmit="return confirm('Yakin ingin menghapus pelanggan ini? Data motor dan riwayat servis terkait juga akan terpengaruh.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Belum ada data pelanggan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pelanggan->hasPages())
            <div class="card-footer">
                {{ $pelanggan->links() }}
            </div>
        @endif
    </div>
@endsection