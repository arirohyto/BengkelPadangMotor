@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Riwayat Servis</a></li>
    <li class="breadcrumb-item active">Edit #{{ $transaksi->id }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span><i class="bi bi-pencil me-2"></i>Edit Transaksi #{{ $transaksi->id }}</span>
                    <span class="badge bg-{{ $transaksi->rekomendasi_badge }}">
                        {{ $transaksi->rekomendasi_servis }}
                    </span>
                </div>
                <div class="card-body">
                    <!-- Info (Read Only) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Pelanggan</label>
                            <p class="fw-medium">{{ $transaksi->pelanggan->nama }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Motor</label>
                            <p class="fw-medium">{{ $transaksi->motor->merk }} - {{ $transaksi->motor->no_polisi }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Tanggal Servis</label>
                            <p class="fw-medium">{{ $transaksi->tanggal_servis->format('d M Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">KM & Kategori</label>
                            <p class="fw-medium">{{ number_format($transaksi->km_saat_ini) }} km -
                                {{ $transaksi->kategori_keluhan }}</p>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('transaksi.update', $transaksi) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="tindakan_mekanik" class="form-label">Tindakan Mekanik</label>
                                <textarea class="form-control @error('tindakan_mekanik') is-invalid @enderror"
                                    id="tindakan_mekanik" name="tindakan_mekanik"
                                    rows="3">{{ old('tindakan_mekanik', $transaksi->tindakan_mekanik) }}</textarea>
                                @error('tindakan_mekanik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="biaya_jasa" class="form-label">Biaya Jasa <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('biaya_jasa') is-invalid @enderror"
                                        id="biaya_jasa" name="biaya_jasa"
                                        value="{{ old('biaya_jasa', $transaksi->biaya_jasa) }}" min="0" required>
                                </div>
                                @error('biaya_jasa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Saat Ini</label>
                                @php
                                    $statusIcons = ['diterima' => 'bi-inbox-fill', 'dikerjakan' => 'bi-gear-fill', 'selesai' => 'bi-check-circle-fill'];
                                    $statusColors = ['diterima' => 'warning', 'dikerjakan' => 'info', 'selesai' => 'success'];
                                    $currentColor = $statusColors[$transaksi->status] ?? 'secondary';
                                    $currentIcon = $statusIcons[$transaksi->status] ?? 'bi-circle';
                                @endphp
                                <div class="form-control bg-{{ $currentColor }}-subtle border-{{ $currentColor }}-subtle text-{{ $currentColor }}-emphasis d-flex align-items-center" style="cursor: default;">
                                    <i class="bi {{ $currentIcon }} me-2"></i>
                                    <strong>{{ ucfirst($transaksi->status) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('transaksi.show', $transaksi) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection