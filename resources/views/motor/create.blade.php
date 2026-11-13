@extends('layouts.app')

@section('title', 'Tambah Motor')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('motor.index') }}">Data Motor</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bicycle me-2"></i>Tambah Motor Baru
            </div>
            <div class="card-body">
                <form action="{{ route('motor.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="id_pelanggan" class="form-label">Pemilik <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_pelanggan') is-invalid @enderror" 
                                    id="id_pelanggan" name="id_pelanggan" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($pelangganList as $p)
                                <option value="{{ $p->id }}" 
                                    {{ old('id_pelanggan', $selectedPelanggan) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} - {{ $p->no_hp }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_pelanggan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_polisi" class="form-label">Nomor Polisi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_polisi') is-invalid @enderror" 
                                   id="no_polisi" name="no_polisi" value="{{ old('no_polisi') }}" 
                                   placeholder="BB 1234 XX" required>
                            @error('no_polisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="merk" class="form-label">Model Motor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('merk') is-invalid @enderror" 
                                   id="merk" name="merk" value="{{ old('merk') }}" 
                                   placeholder="Contoh: Mio M3, NMAX 155, MX King 150" required>
                            @error('merk')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tipe_motor" class="form-label">Tipe Motor <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipe_motor') is-invalid @enderror" 
                                    id="tipe_motor" name="tipe_motor" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach($tipeOptions as $t)
                                <option value="{{ $t }}" {{ old('tipe_motor') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                                @endforeach
                            </select>
                            @error('tipe_motor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="tahun_pembuatan" class="form-label">Tahun Pembuatan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('tahun_pembuatan') is-invalid @enderror" 
                                   id="tahun_pembuatan" name="tahun_pembuatan" 
                                   value="{{ old('tahun_pembuatan', date('Y')) }}" 
                                   min="1990" max="{{ date('Y') }}" required>
                            @error('tahun_pembuatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                        <a href="{{ route('motor.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
