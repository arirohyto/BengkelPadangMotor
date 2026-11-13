@extends('layouts.app')

@section('title', 'Data Sparepart')

@section('breadcrumb')
<li class="breadcrumb-item active">Data Sparepart</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Data Sparepart</h4>
        <p class="text-muted mb-0">Data sparepart bengkel</p>
    </div>
    <a href="{{ route('sparepart.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Tambah Sparepart
    </a>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('sparepart.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari kode part atau nama barang..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $k)
                            <option value="{{ $k }}" {{ request('kategori') == $k ? 'selected' : '' }}>
                                {{ $k }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-check border rounded px-3 py-2 mb-0 d-flex align-items-center bg-light" style="height: 43px;">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="low_stock" value="1" 
                               id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                        <label class="form-check-label mb-0" for="lowStock" style="cursor: pointer; user-select: none;">
                            Stok Menipis
                        </label>
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                    <a href="{{ route('sparepart.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
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
                        <th>Kode Part</th>
                        <th>Nama Barang</th>
                        <th class="text-center">Stok</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Margin</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spareparts as $index => $sp)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-medium">{{ $sp->kode_part }}</td>
                        <td>{{ $sp->nama_barang }}</td>
                        <td class="text-center">
                            @if($sp->is_low_stock)
                            <span class="badge bg-danger" data-bs-toggle="tooltip" title="Stok menipis! Segera lakukan restock" style="cursor: help;">{{ $sp->stok }}</span>
                            @else
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Stok aman" style="cursor: help;">{{ $sp->stok }}</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $sp->harga_beli_formatted }}</td>
                        <td class="text-end">{{ $sp->harga_jual_formatted }}</td>
                        <td class="text-end">
                            @php
                                $margin = $sp->margin;
                                $persen = $sp->presentase_margin;
                                $class = '';
                                if ($persen > 30) {
                                    $class = 'text-success fw-semibold';
                                } elseif ($persen >= 10) {
                                    $class = 'text-warning fw-semibold';
                                } else {
                                    $class = 'text-danger fw-semibold';
                                }
                            @endphp
                            <span class="{{ $class }}">
                                Rp {{ number_format($margin, 0, ',', '.') }}
                            </span>
                            <small class="d-block text-muted" style="font-size: 0.7rem;">({{ number_format($persen, 1) }}%)</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('sparepart.show', $sp) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('sparepart.edit', $sp) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('sparepart.destroy', $sp) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus sparepart ini?')" class="d-inline">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data sparepart
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($spareparts->hasPages())
    <div class="card-footer">
        {{ $spareparts->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (el) {
            return new bootstrap.Tooltip(el)
        });
    });
</script>
@endpush
@endsection
