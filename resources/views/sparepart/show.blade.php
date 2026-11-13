@extends('layouts.app')

@section('title', 'Detail Sparepart - ' . $sparepart->nama_barang)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('sparepart.index') }}">Data Sparepart</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Detail Sparepart</h4>
            <p class="text-muted mb-0">Rincian data stok dan harga sparepart</p>
        </div>
        <a href="{{ route('sparepart.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-box-seam me-2"></i>Rincian Data Sparepart
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Informasi Inventaris</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150" class="text-muted py-2">Kode Part</td>
                            <td class="fw-semibold py-2">{{ $sparepart->kode_part }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Nama Barang</td>
                            <td class="py-2">{{ $sparepart->nama_barang }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Stok</td>
                            <td class="py-2">
                                @if($sparepart->is_low_stock)
                                    <span class="badge bg-danger" data-bs-toggle="tooltip" title="Stok menipis! Segera lakukan restock" style="cursor: help;">
                                        {{ $sparepart->stok }} unit
                                    </span>
                                @else
                                    <span class="badge bg-success" data-bs-toggle="tooltip" title="Stok aman" style="cursor: help;">
                                        {{ $sparepart->stok }} unit
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Kategori Komponen</td>
                            <td class="py-2">
                                @if($sparepart->kategori_komponen)
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $sparepart->kategori_komponen }}</span>
                                @else
                                    <span class="text-muted fst-italic">Tidak ada kategori</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Informasi Keuangan & Sistem</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="150" class="text-muted py-2">Harga Beli</td>
                            <td class="py-2">{{ $sparepart->harga_beli_formatted }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Harga Jual</td>
                            <td class="py-2">{{ $sparepart->harga_jual_formatted }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Margin Keuntungan</td>
                            <td class="py-2">
                                @php
                                    $margin = $sparepart->margin;
                                    $persen = $sparepart->presentase_margin;
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
                                    <span class="text-muted fw-normal" style="font-size: 0.8rem;">({{ number_format($persen, 1) }}%)</span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Dibuat Pada</td>
                            <td class="py-2">{{ $sparepart->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2">Diperbarui Pada</td>
                            <td class="py-2">{{ $sparepart->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('sparepart.edit', $sparepart) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>Edit Data
                </a>

                <form action="{{ route('sparepart.destroy', $sparepart) }}" method="POST" class="d-inline ms-auto"
                    onsubmit="return confirm('Yakin ingin menghapus sparepart ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
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
