@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}">Riwayat Servis</a></li>
    <li class="breadcrumb-item active">Detail #{{ $transaksi->id }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- Transaction Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>Detail Transaksi #{{ $transaksi->id }}
                        <span class="badge bg-{{ $transaksi->status_badge }} ms-2 fs-6">
                            {{ ucfirst($transaksi->status) }}
                        </span>
                    </h5>
                    <small class="text-muted d-block mt-1">
                        Dibuat: {{ $transaksi->created_at->format('d/m/Y H:i') }}
                        @if($transaksi->kasir)
                            · Kasir: <strong>{{ $transaksi->kasir->name }}</strong>
                        @endif
                        @if($transaksi->updated_at != $transaksi->created_at)
                            · Diperbarui: {{ $transaksi->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </small>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informasi Pelanggan</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="120" class="text-muted">Nama</td>
                                    <td>
                                        <a href="{{ route('pelanggan.show', $transaksi->pelanggan) }}">
                                            {{ $transaksi->pelanggan->nama }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No. HP</td>
                                    <td>{{ $transaksi->pelanggan->no_hp }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Motor</td>
                                    <td>{{ $transaksi->motor->merk }} - {{ $transaksi->motor->no_polisi }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tipe</td>
                                    <td><span class="badge bg-secondary">{{ $transaksi->motor->tipe_motor }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informasi Servis</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="140" class="text-muted">Tanggal Servis</td>
                                    <td>{{ $transaksi->tanggal_servis->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Servis Terakhir</td>
                                    <td>
                                        @if($transaksi->tanggal_servis_terakhir)
                                            {{ $transaksi->tanggal_servis_terakhir->format('d M Y') }}
                                            <small class="text-muted">({{ $transaksi->durasi_hari }} hari lalu)</small>
                                        @else
                                            <span class="text-muted">Servis pertama</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">KM Saat Ini</td>
                                    <td>{{ number_format($transaksi->km_saat_ini) }} km</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Usia Motor</td>
                                    <td>{{ $transaksi->usia_motor }} tahun</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Servis Hari Ini</td>
                                    <td><span class="badge bg-primary">{{ $transaksi->jenis_servis ?: '-' }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Kategori Keluhan</h6>
                            <p>{{ $transaksi->kategori_keluhan }}</p>

                            <h6 class="text-muted mb-2">Detail Keluhan</h6>
                            <p>
                                @if($transaksi->keluhan_konsumen)
                                    {{ $transaksi->keluhan_konsumen }}
                                @else
                                    <span class="text-muted fst-italic">Tidak ada catatan</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Tindakan Mekanik</h6>
                            <p>
                                @if($transaksi->tindakan_mekanik)
                                    {{ $transaksi->tindakan_mekanik }}
                                @else
                                    <span class="text-muted fst-italic">Tidak ada catatan</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spareparts Used -->
            @if($transaksi->spareparts->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-box-seam me-2"></i>Sparepart yang Digunakan
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->spareparts as $sp)
                                    <tr>
                                        <td>{{ $sp->kode_part }}</td>
                                        <td>{{ $sp->nama_barang }}</td>
                                        <td class="text-end">Rp {{ number_format($sp->pivot->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $sp->pivot->jumlah }}</td>
                                        <td class="text-end">Rp {{ number_format($sp->pivot->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">Total Sparepart:</td>
                                    <td></td>
                                    <td class="fw-semibold text-end">
                                        Rp {{ number_format($transaksi->spareparts->sum(fn($s) => $s->pivot->subtotal), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Status Progress -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-signpost-2 me-2"></i>Alur Status Servis
                </div>
                <div class="card-body">
                    @php
                        $steps = [
                            'diterima' => ['label' => 'Diterima', 'icon' => 'bi-inbox-fill', 'desc' => 'Motor diterima, keluhan dicatat oleh kasir'],
                            'dikerjakan' => ['label' => 'Dikerjakan', 'icon' => 'bi-gear-fill', 'desc' => 'Motor sedang dikerjakan oleh mekanik'],
                            'selesai' => ['label' => 'Selesai', 'icon' => 'bi-check-circle-fill', 'desc' => 'Servis selesai, pembayaran dikonfirmasi'],
                        ];
                        $statusOrder = ['diterima', 'dikerjakan', 'selesai'];
                        $currentIdx = array_search($transaksi->status, $statusOrder);
                    @endphp
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        @foreach($steps as $key => $step)
                            @php
                                $stepIdx = array_search($key, $statusOrder);
                                $isActive = $key === $transaksi->status;
                                $isDone = $stepIdx < $currentIdx;
                                $isUpcoming = $stepIdx > $currentIdx;
                            @endphp
                            <div class="text-center flex-fill">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                     style="width: 40px; height: 40px; 
                                        {{ $isDone ? 'background: #198754; color: white;' : '' }}
                                        {{ $isActive ? 'background: #0d6efd; color: white; box-shadow: 0 0 0 3px rgba(13,110,253,0.3);' : '' }}
                                        {{ $isUpcoming ? 'background: #e9ecef; color: #adb5bd;' : '' }}">
                                    @if($isDone)
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        <i class="bi {{ $step['icon'] }}"></i>
                                    @endif
                                </div>
                                <div class="small {{ $isActive ? 'fw-bold text-primary' : ($isDone ? 'text-success' : 'text-muted') }}">
                                    {{ $step['label'] }}
                                </div>
                            </div>
                            @if(!$loop->last)
                                <div class="flex-fill px-2" style="max-width: 60px;">
                                    <hr class="my-0" style="border-top: 2px {{ $stepIdx < $currentIdx ? 'solid #198754' : 'dashed #dee2e6' }};">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="alert alert-{{ $transaksi->status === 'selesai' ? 'success' : ($transaksi->status === 'dikerjakan' ? 'info' : 'warning') }} py-2 mb-0 small">
                        <i class="bi {{ $steps[$transaksi->status]['icon'] }} me-1"></i>
                        {{ $steps[$transaksi->status]['desc'] }}
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($transaksi->status !== 'selesai')
                            <a href="{{ route('transaksi.edit', $transaksi) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                        @endif

                        @if($transaksi->status === 'selesai')
                            <a href="{{ route('transaksi.nota', $transaksi) }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-printer me-1"></i>Cetak Nota
                            </a>
                        @endif

                        @if($transaksi->status === 'diterima')
                            <form action="{{ route('transaksi.update-status', $transaksi) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info text-white"
                                    onclick="return confirm('Mulai kerjakan servis untuk transaksi #{{ $transaksi->id }}?')">
                                    <i class="bi bi-play-fill me-1"></i>Mulai Kerjakan
                                </button>
                            </form>
                        @elseif($transaksi->status === 'dikerjakan')
                            <form action="{{ route('transaksi.update-status', $transaksi) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Selesaikan servis dan konfirmasi pembayaran untuk transaksi #{{ $transaksi->id }}?')">
                                    <i class="bi bi-cash-coin me-1"></i>Selesai & Bayar
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('transaksi.destroy', $transaksi) }}" method="POST" class="d-inline ms-auto"
                            onsubmit="return confirm('Yakin ingin menghapus transaksi #{{ $transaksi->id }}? Data tidak dapat dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </form>

                        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prediction Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-diagram-3 me-2"></i>Rekomendasi Servis Selanjutnya
                </div>
                <div class="card-body">
                    <div class="prediction-box text-center mb-3">
                        <small class="text-muted d-block mb-1">Hasil Prediksi CART (KM +3000)</small>
                        <div class="prediction-result text-{{ $transaksi->rekomendasi_badge }} fw-bold">
                            {{ $transaksi->rekomendasi_servis }}
                        </div>
                    </div>

                    @if(in_array($transaksi->rekomendasi_servis, ['Ganti Part Fast Moving', 'Servis CVT', 'Servis Besar']) && !empty($transaksi->kritis_components))
                        @php
                            $nama_kritis = array_map(function($komp) {
                                return $komp['nama'];
                            }, $transaksi->kritis_components);
                            $nama_kritis_str = implode(', ', $nama_kritis);
                        @endphp
                        <div class="bg-light rounded p-3 mb-3">
                            <small class="text-muted d-block mb-2">Deskripsi:</small>
                            @if($transaksi->rekomendasi_servis === 'Servis Besar')
                                <p class="mb-0 small">Seluruh komponen KSB telah melewati batas pakai: <strong>{{ $nama_kritis_str }}</strong>. Disarankan melakukan Servis Besar (overhaul) untuk pemeriksaan menyeluruh dan penggantian seluruh komponen kritis pada kunjungan berikutnya.</p>
                            @elseif($transaksi->rekomendasi_servis === 'Servis CVT')
                                <p class="mb-0 small">Komponen kritis yang melewati batas pakai: <strong>{{ $nama_kritis_str }}</strong>. Disarankan melakukan Servis CVT untuk melakukan pemeriksaan dan penggantian komponen tersebut pada kunjungan berikutnya.</p>
                            @else
                                <p class="mb-0 small">Komponen kritis yang melewati batas pakai: <strong>{{ $nama_kritis_str }}</strong>. Disarankan untuk melakukan pemeriksaan dan penggantian pada kunjungan berikutnya.</p>
                            @endif
                        </div>
                        
                        <div class="bg-danger-subtle border border-danger-subtle rounded p-3 mb-3 text-danger-emphasis">
                            <h6 class="fw-bold mb-2 small"><i class="bi bi-exclamation-triangle-fill me-1"></i>Komponen Perlu Perhatian:</h6>
                            <ul class="mb-0 ps-3 small">
                                @foreach($transaksi->kritis_components as $komp)
                                    <li>
                                        <strong>{{ $komp['nama'] }}</strong><br>
                                        <small class="text-muted">
                                            Pemakaian: {{ number_format($komp['delta_km']) }} / {{ number_format($komp['limit_km']) }} km<br>
                                            Penggantian terakhir: {{ $komp['last_replaced_km'] > 0 ? number_format($komp['last_replaced_km']) . ' km' : 'Bawaan Pabrik' }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif($transaksi->wasRekomendasiOverridden())
                        <div class="bg-light rounded p-3 mb-3">
                            <small class="text-muted d-block mb-2">Deskripsi:</small>
                            @if($transaksi->rekomendasi_servis === 'Servis CVT')
                                <p class="mb-0 small">Seluruh komponen fast moving diprediksikan masih aman karena baru saja diganti. Disarankan melakukan servis CVT rutin pada kunjungan berikutnya.</p>
                            @else
                                <p class="mb-0 small">Seluruh komponen fast moving diprediksikan masih aman karena baru saja diganti. Lakukan pengecekan rutin berkala seperti biasa pada kunjungan berikutnya.</p>
                            @endif
                        </div>
                    @else
                        <div class="bg-light rounded p-3 mb-3">
                            <small class="text-muted d-block mb-2">Deskripsi:</small>
                            <p class="mb-0 small">Disarankan melakukan {{ $transaksi->rekomendasi_servis }} sesuai dengan pola interval kendaraan Anda.</p>
                        </div>
                    @endif

                    <a href="{{ route('transaksi.create', [
                                'motor_id' => $transaksi->id_motor,
                                'pelanggan_id' => $transaksi->id_pelanggan
                              ]) }}" 
                       class="btn btn-primary btn-sm w-100 mt-2 mb-3">
                        <i class="bi bi-plus-lg me-1"></i>
                        Buat Servis Berikutnya
                    </a>

                    @php
                        // Calculate forward-feeding projections for display
                        $projectedKm = $transaksi->km_saat_ini + 3000;
                        $projectedDurasi = 90;
                        if ($transaksi->jenis_servis === 'Ganti Part Fast Moving') {
                            $lastRoutine = \App\Models\TransaksiServis::where('id_motor', $transaksi->id_motor)
                                ->where('id', '<', $transaksi->id)
                                ->whereIn('jenis_servis', ['Servis Ringan', 'Servis CVT', 'Servis Besar'])
                                ->latest('tanggal_servis')
                                ->first();
                            if ($lastRoutine) {
                                $days = \Carbon\Carbon::parse($lastRoutine->tanggal_servis)->diffInDays(\Carbon\Carbon::parse($transaksi->tanggal_servis));
                                if ($days < 90) {
                                    $projectedDurasi = 90 - $days;
                                }
                            }
                        }

                        $projectedKeluhan = 'Rutin';
                        $kritis = $transaksi->kritis_components;
                        if (!empty($kritis)) {
                            $firstKritis = $kritis[0]['nama'];
                            $projectedKeluhan = match ($firstKritis) {
                                'Busi' => 'Performa',
                                'Kampas Rem' => 'Pengereman',
                                'Filter Udara' => 'Performa',
                                'Aki' => 'Kelistrikan',
                                'V-Belt', 'Roller CVT' => 'CVT',
                                'Rantai Gear' => 'Rantai',
                                default => 'Rutin'
                            };
                        }
                    @endphp

                    <hr>

                    <h6 class="text-muted mb-3"><i class="bi bi-arrow-right-circle me-1"></i>Input Proyeksi CART:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Durasi Hari Proyeksi</td>
                            <td class="text-end fw-medium">{{ $projectedDurasi }} hari</td>
                        </tr>
                        <tr>
                            <td class="text-muted">KM Tempuh Proyeksi</td>
                            <td class="text-end fw-medium">{{ number_format($projectedKm) }} km</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Usia Motor</td>
                            <td class="text-end fw-medium">{{ $transaksi->usia_motor }} tahun</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kategori Keluhan</td>
                            <td class="text-end fw-medium">{{ $projectedKeluhan }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe Motor</td>
                            <td class="text-end fw-medium">{{ $transaksi->motor->tipe_motor }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-cash me-2"></i>Rincian Biaya
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>Biaya Jasa</td>
                            <td class="text-end">Rp {{ number_format($transaksi->biaya_jasa, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Biaya Sparepart</td>
                            <td class="text-end">Rp {{ number_format($transaksi->total_sparepart, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold">Total</td>
                            <td class="text-end fw-bold fs-5 text-primary">
                                {{ $transaksi->total_biaya_formatted }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection