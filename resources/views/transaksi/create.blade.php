@extends('layouts.app')

@section('title', 'Input Servis Baru')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Riwayat Servis</a></li>
<li class="breadcrumb-item active">Input Servis Baru</li>
@endsection

@section('content')
<div class="row">
    <!-- Form Input -->
    <div class="col-lg-8">
        <form action="{{ route('transaksi.store') }}" method="POST" id="formServis">
            @csrf
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i>Data Pelanggan & Motor
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="id_pelanggan" class="form-label">Pelanggan <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_pelanggan') is-invalid @enderror" 
                                    id="id_pelanggan" name="id_pelanggan" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($pelangganList as $p)
                                <option value="{{ $p->id }}" 
                                    {{ (old('id_pelanggan', $selectedPelanggan?->id) == $p->id) ? 'selected' : '' }}>
                                    {{ $p->nama }} - {{ $p->no_hp }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_pelanggan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="id_motor" class="form-label">Motor <span class="text-danger">*</span></label>
                            <select class="form-select @error('id_motor') is-invalid @enderror" 
                                    id="id_motor" name="id_motor" required>
                                <option value="">-- Pilih Motor --</option>
                                @if($selectedPelanggan)
                                    @foreach($selectedPelanggan->motors as $m)
                                    <option value="{{ $m->id }}" 
                                            data-tipe="{{ $m->tipe_motor }}"
                                            data-tahun="{{ $m->tahun_pembuatan }}"
                                        {{ (old('id_motor', $selectedMotor?->id) == $m->id) ? 'selected' : '' }}>
                                        {{ $m->no_polisi }} - {{ $m->merk }} ({{ $m->tipe_motor }})
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('id_motor')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Info Servis Terakhir -->
                    <div id="infoServisTerakhir" class="alert alert-info mt-3 d-none">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Servis Terakhir:</strong> 
                        <span id="tanggalServisTerakhir">-</span>
                        (<span id="durasiHari">0</span> hari yang lalu)
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-wrench me-2"></i>Data Servis
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal_servis" class="form-label">Tanggal Servis <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_servis') is-invalid @enderror" 
                                   id="tanggal_servis" name="tanggal_servis" 
                                   value="{{ old('tanggal_servis', date('Y-m-d')) }}" required>
                            @error('tanggal_servis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="km_saat_ini" class="form-label">KM Saat Ini <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('km_saat_ini') is-invalid @enderror" 
                                   id="km_saat_ini" name="km_saat_ini" 
                                   value="{{ old('km_saat_ini') }}" 
                                   placeholder="Contoh: 25000" required>
                            @error('km_saat_ini')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kategori_keluhan" class="form-label">Kategori Keluhan <span class="text-danger">*</span></label>
                            <select class="form-select @error('kategori_keluhan') is-invalid @enderror" 
                                    id="kategori_keluhan" name="kategori_keluhan" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoriKeluhan as $k)
                                <option value="{{ $k }}" {{ old('kategori_keluhan') == $k ? 'selected' : '' }}>
                                    {{ $k }}
                                </option>
                                @endforeach
                            </select>
                            @error('kategori_keluhan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_servis" class="form-label">Jenis Servis Hari Ini <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_servis') is-invalid @enderror" 
                                    id="jenis_servis" name="jenis_servis" required>
                                <option value="">-- Pilih Jenis Servis --</option>
                                @foreach($jenisServisList as $js)
                                <option value="{{ $js }}" {{ old('jenis_servis') == $js ? 'selected' : '' }}>
                                    {{ $js }}
                                </option>
                                @endforeach
                            </select>
                            @error('jenis_servis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="keluhan_konsumen" class="form-label">Detail Keluhan</label>
                            <textarea class="form-control" id="keluhan_konsumen" name="keluhan_konsumen" 
                                      rows="2" placeholder="Deskripsikan keluhan pelanggan...">{{ old('keluhan_konsumen') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label for="tindakan_mekanik" class="form-label">Tindakan Mekanik</label>
                            <textarea class="form-control" id="tindakan_mekanik" name="tindakan_mekanik" 
                                      rows="2" placeholder="Tindakan yang dilakukan...">{{ old('tindakan_mekanik') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-box-seam me-2"></i>Suku Cadang (Yamaha Genuine Parts)</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahSparepart">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Sparepart
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle d-none" id="tableSpareparts">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Suku Cadang</th>
                                    <th width="150">Harga Satuan</th>
                                    <th width="100">Jumlah</th>
                                    <th width="150">Subtotal</th>
                                    <th width="60" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sparepartsContainer">
                                <!-- Dynamic rows added here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="sparepartsEmpty" class="text-center py-3 text-muted">
                        <i class="bi bi-info-circle me-1"></i> Tidak ada sparepart yang ditambahkan hari ini.
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-cash me-2"></i>Biaya
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="biaya_jasa" class="form-label">Biaya Jasa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('biaya_jasa') is-invalid @enderror" 
                                       id="biaya_jasa" name="biaya_jasa" 
                                       value="{{ old('biaya_jasa', 0) }}" min="0" required>
                            </div>
                            @error('biaya_jasa')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Simpan Transaksi
                </button>
                <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-x-lg me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Sidebar Riwayat Servis -->
    <div class="col-lg-4">
        <div class="card sticky-top shadow-sm" style="top: 80px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; z-index: 10;">
            <div class="card-header text-white" style="background-color: #0f172a; padding: 1.25rem;">
                <h5 class="card-title mb-0 d-flex align-items-center fs-6 fw-bold">
                    Riwayat Servis Motor
                </h5>
            </div>
            <div class="card-body" style="background-color: #ffffff; padding: 1.5rem; min-height: 280px;">
                <!-- Loading Spinner -->
                <div id="riwayatLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted small">Mengambil riwayat servis...</p>
                </div>

                <!-- Empty/Initial State -->
                <div id="riwayatEmpty" class="text-center py-5 text-muted">
                    <p class="mt-3 mb-0 small">Pilih motor untuk melihat riwayat servis</p>
                </div>

                <!-- Result Container -->
                <div id="riwayatResult" class="d-none">
                    <!-- Motor Brief Info Card -->
                    <div class="p-3 rounded mb-4" style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                        <h6 class="fw-bold mb-3 d-flex align-items-center text-dark" id="motorNamePlate">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            <span id="motorName">-</span> (<span id="motorPlate">-</span>)
                        </h6>
                        <div class="row g-2 text-muted" style="font-size: 0.8rem;">
                            <div class="col-6">
                                <div>Total Servis</div>
                                <div class="fw-bold text-dark fs-6" id="totalServis">0 kali</div>
                            </div>
                            <div class="col-6">
                                <div>KM Terakhir</div>
                                <div class="fw-bold text-dark fs-6" id="kmTerakhir">-</div>
                            </div>
                            <div class="col-12 mt-2">
                                <div>Servis Terakhir</div>
                                <div class="fw-bold text-dark" id="tanggalTerakhir">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline List of Last 5 Services -->
                    <h6 class="fw-bold text-dark mb-3" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        Timeline Servis Terakhir
                    </h6>
                    <div class="timeline-container position-relative ps-3" id="timelineContainer" style="border-left: 2px solid #e2e8f0; margin-left: 8px;">
                        <!-- Dynamic items loaded here -->
                    </div>

                    <!-- Footer Panel -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="#" id="linkSemuaRiwayat" class="text-primary fw-bold text-decoration-none small">
                            Lihat semua riwayat <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pelangganSelect = document.getElementById('id_pelanggan');
    const motorSelect = document.getElementById('id_motor');
    const tanggalServis = document.getElementById('tanggal_servis');
    const kmSaatIni = document.getElementById('km_saat_ini');
    const kategoriKeluhan = document.getElementById('kategori_keluhan');
    const infoServisTerakhir = document.getElementById('infoServisTerakhir');
    
    const jenisServis = document.getElementById('jenis_servis');
    
    // Load motors when pelanggan changes
    pelangganSelect.addEventListener('change', function() {
        const pelangganId = this.value;
        motorSelect.innerHTML = '<option value="">-- Pilih Motor --</option>';
        infoServisTerakhir.classList.add('d-none');
        
        if (pelangganId) {
            fetch(`/pelanggan/${pelangganId}/motors`)
                .then(res => res.json())
                .then(data => {
                    data.motors.forEach(motor => {
                        const option = document.createElement('option');
                        option.value = motor.id;
                        option.textContent = `${motor.no_polisi} - ${motor.merk} (${motor.tipe_motor})`;
                        option.dataset.tipe = motor.tipe_motor;
                        option.dataset.tahun = motor.tahun_pembuatan;
                        motorSelect.appendChild(option);
                    });
                });
        }
    });

    // Load last service and full history panel when motor changes
    motorSelect.addEventListener('change', function() {
        const motorId = this.value;
        
        if (motorId) {
            fetch(`/api/motor/${motorId}/last-service`)
                .then(res => res.json())
                .then(data => {
                    if (data.data) {
                        document.getElementById('tanggalServisTerakhir').textContent = data.data.tanggal_servis;
                        const lastDate = new Date(data.data.tanggal_servis);
                        const today = new Date(tanggalServis.value);
                        const diffDays = Math.floor((today - lastDate) / (1000 * 60 * 60 * 24));
                        document.getElementById('durasiHari').textContent = diffDays;
                        infoServisTerakhir.classList.remove('d-none');
                    } else {
                        infoServisTerakhir.classList.add('d-none');
                    }
                });

            loadRiwayatPanel(motorId);
        } else {
            document.getElementById('riwayatEmpty').classList.remove('d-none');
            document.getElementById('riwayatLoading').classList.add('d-none');
            document.getElementById('riwayatResult').classList.add('d-none');
            infoServisTerakhir.classList.add('d-none');
        }
    });

    function loadRiwayatPanel(motorId) {
        document.getElementById('riwayatEmpty').classList.add('d-none');
        document.getElementById('riwayatLoading').classList.remove('d-none');
        document.getElementById('riwayatResult').classList.add('d-none');

        fetch(`/servis/riwayat-motor/${motorId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('riwayatLoading').classList.add('d-none');

                if (data.success && data.data) {
                    const resData = data.data;
                    
                    // Update Motor Info
                    document.getElementById('motorName').textContent = `${resData.motor.merk} (${resData.motor.tipe_motor})`;
                    document.getElementById('motorPlate').textContent = resData.motor.no_polisi;
                    document.getElementById('totalServis').textContent = `${resData.total_servis} kali`;
                    document.getElementById('kmTerakhir').textContent = resData.km_terakhir;
                    document.getElementById('tanggalTerakhir').textContent = resData.tanggal_terakhir;

                    // Update Filter Link
                    document.getElementById('linkSemuaRiwayat').href = `{{ route('transaksi.index') }}?search=${resData.motor.no_polisi}`;

                    // Update Timeline
                    const container = document.getElementById('timelineContainer');
                    container.innerHTML = '';

                    if (resData.riwayat && resData.riwayat.length > 0) {
                        resData.riwayat.forEach(item => {
                            let bgClass = 'bg-secondary';
                            if (item.status === 'selesai') bgClass = 'bg-success';
                            else if (item.status === 'dikerjakan') bgClass = 'bg-info';
                            else if (item.status === 'diterima') bgClass = 'bg-warning';

                            const timelineItem = document.createElement('div');
                            timelineItem.className = 'timeline-item position-relative mb-4';
                            timelineItem.innerHTML = `
                                <div class="position-absolute rounded-circle bg-white border border-primary" style="width: 12px; height: 12px; left: -22px; top: 4px;"></div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">${item.jenis_servis}</span>
                                    <span class="badge ${bgClass} small text-capitalize">${item.status}</span>
                                </div>
                                <div class="text-muted mb-1" style="font-size: 0.75rem;">
                                    <span>${item.tanggal_servis_formatted}</span> &bull; <span>${item.km_saat_ini} KM</span> &bull; <span class="text-secondary fw-medium">${item.kategori_keluhan}</span>
                                </div>
                                <div class="text-dark bg-light p-2 rounded small border-start border-3" style="font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${item.tindakan_mekanik.replace(/"/g, '&quot;')}">
                                    ${item.tindakan_mekanik}
                                </div>
                            `;
                            container.appendChild(timelineItem);
                        });
                        document.getElementById('riwayatResult').classList.remove('d-none');
                    } else {
                        container.innerHTML = `
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0 small">Belum ada riwayat servis untuk motor ini</p>
                            </div>
                        `;
                        document.getElementById('riwayatResult').classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                console.error('Failed to load history:', err);
                document.getElementById('riwayatLoading').classList.add('d-none');
                document.getElementById('riwayatEmpty').classList.remove('d-none');
            });
    }

    const btnTambahSparepart = document.getElementById('btnTambahSparepart');
    const tableSpareparts = document.getElementById('tableSpareparts');
    const sparepartsContainer = document.getElementById('sparepartsContainer');
    const sparepartsEmpty = document.getElementById('sparepartsEmpty');
    let sparepartIndex = 0;

    btnTambahSparepart.addEventListener('click', function() {
        tableSpareparts.classList.remove('d-none');
        sparepartsEmpty.classList.add('d-none');

        const row = document.createElement('tr');
        row.id = `sparepart-row-${sparepartIndex}`;

        let optionsHtml = '<option value="">-- Pilih Sparepart --</option>';
        @foreach($sparepartList as $s)
            optionsHtml += '<option value="{{ $s->id }}" data-harga="{{ $s->harga_jual }}" data-stok="{{ $s->stok }}">{{ $s->nama_barang }} (Stok: {{ $s->stok }})</option>';
        @endforeach

        row.innerHTML = `
            <td>
                <select name="spareparts[${sparepartIndex}][id]" class="form-select select-sparepart" required>
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control bg-light harga-display text-end" readonly value="0">
                </div>
            </td>
            <td>
                <input type="number" name="spareparts[${sparepartIndex}][jumlah]" class="form-control form-control-sm jumlah-input" value="1" min="1" required disabled>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control bg-light subtotal-display text-end" readonly value="0">
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        sparepartsContainer.appendChild(row);

        const select = row.querySelector('.select-sparepart');
        const jumlahInput = row.querySelector('.jumlah-input');
        const hargaDisplay = row.querySelector('.harga-display');
        const subtotalDisplay = row.querySelector('.subtotal-display');
        const btnHapus = row.querySelector('.btn-hapus-row');

        select.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                const harga = parseFloat(option.dataset.harga);
                const stok = parseInt(option.dataset.stok);
                hargaDisplay.value = harga.toLocaleString('id-ID');
                jumlahInput.disabled = false;
                jumlahInput.max = stok;
                jumlahInput.value = 1;
                subtotalDisplay.value = harga.toLocaleString('id-ID');
            } else {
                hargaDisplay.value = '0';
                jumlahInput.disabled = true;
                jumlahInput.value = '';
                subtotalDisplay.value = '0';
            }
        });

        jumlahInput.addEventListener('input', function() {
            const option = select.options[select.selectedIndex];
            if (option.value) {
                const harga = parseFloat(option.dataset.harga);
                const jumlah = parseInt(this.value) || 0;
                const subtotal = harga * jumlah;
                subtotalDisplay.value = subtotal.toLocaleString('id-ID');
            }
        });

        btnHapus.addEventListener('click', function() {
            row.remove();
            if (sparepartsContainer.children.length === 0) {
                tableSpareparts.classList.add('d-none');
                sparepartsEmpty.classList.remove('d-none');
            }
        });

        sparepartIndex++;
    });

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    if (motorSelect.value) {
        fetch(`/api/motor/${motorSelect.value}/last-service`)
            .then(res => res.json())
            .then(data => {
                if (data.data) {
                    document.getElementById('tanggalServisTerakhir').textContent = data.data.tanggal_servis;
                    const lastDate = new Date(data.data.tanggal_servis);
                    const today = new Date(tanggalServis.value);
                    const diffDays = Math.floor((today - lastDate) / (1000 * 60 * 60 * 24));
                    document.getElementById('durasiHari').textContent = diffDays;
                    infoServisTerakhir.classList.remove('d-none');
                } else {
                    infoServisTerakhir.classList.add('d-none');
                }
            });
        loadRiwayatPanel(motorSelect.value);
    }
});
</script>
@endpush
