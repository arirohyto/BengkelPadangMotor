@extends('layouts.app')

@section('title', 'Prediksi Servis Motor')

@section('breadcrumb')
    <li class="breadcrumb-item active">Prediksi Servis Motor</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-cpu me-2"></i>Prediksi Servis Motor - Algoritma CART
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Masukkan data kondisi motor untuk mendapatkan rekomendasi jenis servis
                        menggunakan algoritma <strong>CART (Classification and Regression Trees)</strong>
                        dengan criterion Gini.
                    </p>

                    <!-- API Status -->
                    <div class="alert {{ $apiStatus ? 'alert-success' : 'alert-warning' }} py-2 mb-4">
                        <i class="bi {{ $apiStatus ? 'bi-check-circle' : 'bi-exclamation-triangle' }} me-2"></i>
                        <strong>Status Model:</strong>
                        @if($apiStatus)
                            Terhubung ke Python API
                            @if($modelInfo)
                                (Akurasi: {{ number_format(($modelInfo['accuracy'] ?? 0) * 100, 1) }}%)
                            @endif
                        @else
                            Menggunakan rules-based (API tidak tersedia)
                        @endif
                    </div>

                    <form id="formPrediksi">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="durasi_hari" class="form-label">
                                    <i class="bi bi-calendar3 me-1"></i>Durasi Hari
                                </label>
                                <input type="number" class="form-control" id="durasi_hari" name="durasi_hari" value="30"
                                    min="0" required>
                                <small class="text-muted">Hari sejak servis terakhir</small>
                            </div>
                            <div class="col-md-6">
                                <label for="km_tempuh" class="form-label">
                                    <i class="bi bi-speedometer2 me-1"></i>KM Tempuh
                                </label>
                                <input type="number" class="form-control" id="km_tempuh" name="km_tempuh" value="15000"
                                    min="0" required>
                                <small class="text-muted">Kilometer saat ini</small>
                            </div>
                            <div class="col-md-6">
                                <label for="usia_motor" class="form-label">
                                    <i class="bi bi-clock-history me-1"></i>Usia Motor (Tahun)
                                </label>
                                <input type="number" class="form-control" id="usia_motor" name="usia_motor" value="3"
                                    min="0" max="30" required>
                                <small class="text-muted">Umur kendaraan dalam tahun</small>
                            </div>
                            <div class="col-md-6">
                                <label for="tipe_motor" class="form-label">
                                    <i class="bi bi-bicycle me-1"></i>Tipe Motor
                                </label>
                                <select class="form-select" id="tipe_motor" name="tipe_motor" required>
                                    @foreach($tipeMotor as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="kategori_keluhan" class="form-label">
                                    <i class="bi bi-exclamation-circle me-1"></i>Keluhan Utama
                                </label>
                                <select class="form-select" id="kategori_keluhan" name="kategori_keluhan" required>
                                    @foreach($kategoriKeluhan as $k)
                                        <option value="{{ $k }}">{{ $k }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-lightning-charge me-2"></i>Prediksi Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Model Info Card -->
            @if($modelInfo)
                <div class="card mt-4">
                    <div class="card-header">
                        <i class="bi bi-info-circle me-2"></i>Informasi Model CART
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Jumlah Data Training</small>
                                <strong>{{ $modelInfo['n_samples'] ?? '-' }} sampel</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Akurasi Model</small>
                                <strong>{{ number_format(($modelInfo['accuracy'] ?? 0) * 100, 2) }}%</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Kedalaman Pohon</small>
                                <strong>{{ $modelInfo['tree_depth'] ?? '-' }} level</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Jumlah Leaf Nodes</small>
                                <strong>{{ $modelInfo['n_leaves'] ?? '-' }} nodes</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-bullseye me-2"></i>Hasil Prediksi
                </div>
                <div class="card-body">
                    <div id="hasilLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memproses prediksi dengan algoritma CART...</p>
                    </div>

                    <div id="hasilEmpty" class="text-center py-5">
                        <i class="bi bi-hand-index text-muted" style="font-size: 4rem;"></i>
                        <p class="mt-3 text-muted">
                            Isi form di sebelah kiri dan klik<br>
                            <strong>"Prediksi Sekarang"</strong> untuk melihat hasil
                        </p>
                    </div>

                    <div id="hasilPrediksi" class="d-none">
                        <!-- Recommendation Result -->
                        <div class="prediction-box text-center mb-4">
                            <small class="text-muted d-block mb-2">Rekomendasi Servis</small>
                            <div id="resultRekomendasi" class="prediction-result display-6 fw-bold">-</div>
                        </div>

                        <!-- Confidence -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Tingkat Keyakinan</span>
                                <span id="confidenceValue" class="fw-medium">0%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div id="confidenceBar" class="progress-bar bg-success" role="progressbar"
                                    style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-light rounded p-3 mb-4">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-info-circle me-1"></i>Deskripsi:
                            </small>
                            <p id="resultDescription" class="mb-0">-</p>
                        </div>

                        <!-- Source Badge -->
                        <div class="text-center mb-4">
                            <span id="resultSource" class="badge bg-info">-</span>
                        </div>

                        <hr>

                        <!-- Input Summary -->
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-input-cursor me-2"></i>Input yang Diproses:
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="50%">Durasi Hari</td>
                                <td class="text-end fw-medium" id="resultDurasi">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">KM Tempuh</td>
                                <td class="text-end fw-medium" id="resultKm">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Usia Motor</td>
                                <td class="text-end fw-medium" id="resultUsia">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tipe Motor</td>
                                <td class="text-end fw-medium" id="resultTipe">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keluhan</td>
                                <td class="text-end fw-medium" id="resultKeluhan">-</td>
                            </tr>
                        </table>

                        <!-- Probabilities (if available) -->
                        <div id="probabilitiesSection" class="mt-4 d-none">
                            <h6 class="text-muted mb-3">
                                <i class="bi bi-bar-chart me-2"></i>Probabilitas Semua Kelas:
                            </h6>
                            <div id="probabilitiesContainer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Tentang Algoritma CART
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Apa itu CART?</h6>
                            <p class="text-muted mb-3">
                                <strong>CART (Classification and Regression Trees)</strong> adalah algoritma
                                pohon keputusan yang digunakan untuk klasifikasi. Algoritma ini menggunakan
                                <strong>Gini Impurity</strong> sebagai kriteria pembelahan node.
                            </p>

                            <h6>Fitur Input (X):</h6>
                            <ul class="text-muted">
                                <li><strong>Durasi</strong> - Hari sejak servis terakhir</li>
                                <li><strong>KM</strong> - Kilometer saat ini</li>
                                <li><strong>Usia</strong> - Umur motor dalam tahun</li>
                                <li><strong>Tipe</strong> - Kategori motor</li>
                                <li><strong>Keluhan</strong> - Jenis keluhan utama</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Output Prediksi (Y):</h6>
                            <ul class="list-unstyled">
                                @foreach($rekomendasiOptions as $r)
                                    <li class="mb-2">
                                        <span class="badge bg-secondary me-2">{{ $r }}</span>
                                        <small
                                            class="text-muted">{{ \App\Services\CARTPredictor::getRekomendasiDescription($r) }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .prediction-box {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 16px;
            padding: 2rem;
        }

        .prediction-result.text-success {
            color: #22c55e !important;
        }

        .prediction-result.text-info {
            color: #06b6d4 !important;
        }

        .prediction-result.text-warning {
            color: #f59e0b !important;
        }

        .prediction-result.text-danger {
            color: #ef4444 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('formPrediksi').addEventListener('submit', function (e) {
            e.preventDefault();

            // Show loading
            document.getElementById('hasilEmpty').classList.add('d-none');
            document.getElementById('hasilLoading').classList.remove('d-none');
            document.getElementById('hasilPrediksi').classList.add('d-none');

            const formData = {
                durasi_hari: parseInt(document.getElementById('durasi_hari').value),
                km_tempuh: parseInt(document.getElementById('km_tempuh').value),
                usia_motor: parseInt(document.getElementById('usia_motor').value),
                tipe_motor: document.getElementById('tipe_motor').value,
                kategori_keluhan: document.getElementById('kategori_keluhan').value,
            };

            fetch('{{ route("prediksi.test.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('hasilLoading').classList.add('d-none');

                    if (data.success) {
                        document.getElementById('hasilPrediksi').classList.remove('d-none');

                        // Set result
                        const resultEl = document.getElementById('resultRekomendasi');
                        resultEl.textContent = data.data.rekomendasi;
                        resultEl.className = 'prediction-result display-6 fw-bold';

                        // Color based on recommendation
                        switch (data.data.rekomendasi) {
                            case 'Servis Ringan': resultEl.classList.add('text-success'); break;
                            case 'Servis CVT': resultEl.classList.add('text-info'); break;
                            case 'Servis Besar': resultEl.classList.add('text-warning'); break;
                            case 'Ganti Part Fast Moving': resultEl.classList.add('text-danger'); break;
                        }

                        // Confidence
                        const confidence = (data.data.confidence * 100).toFixed(1);
                        document.getElementById('confidenceValue').textContent = confidence + '%';
                        document.getElementById('confidenceBar').style.width = confidence + '%';

                        // Description
                        document.getElementById('resultDescription').textContent = data.data.description;

                        // Source
                        const sourceEl = document.getElementById('resultSource');
                        if (data.data.source === 'api') {
                            sourceEl.textContent = 'Prediksi dari Model CART (Python API)';
                            sourceEl.className = 'badge bg-success';
                        } else {
                            sourceEl.textContent = 'Prediksi dari Rules-Based (Fallback)';
                            sourceEl.className = 'badge bg-warning text-dark';
                        }

                        // Input summary
                        document.getElementById('resultDurasi').textContent = data.data.input.durasi_hari + ' hari';
                        document.getElementById('resultKm').textContent = data.data.input.km_tempuh.toLocaleString() + ' km';
                        document.getElementById('resultUsia').textContent = data.data.input.usia_motor + ' tahun';
                        document.getElementById('resultTipe').textContent = data.data.input.tipe_motor;
                        document.getElementById('resultKeluhan').textContent = data.data.input.kategori_keluhan;

                        // Probabilities (if available from API)
                        if (data.data.probabilities && Object.keys(data.data.probabilities).length > 0) {
                            document.getElementById('probabilitiesSection').classList.remove('d-none');
                            const container = document.getElementById('probabilitiesContainer');
                            container.innerHTML = '';

                            for (const [cls, prob] of Object.entries(data.data.probabilities)) {
                                const percentage = (prob * 100).toFixed(1);
                                container.innerHTML += `
                            <div class="mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>${cls}</small>
                                    <small>${percentage}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar" style="width: ${percentage}%"></div>
                                </div>
                            </div>
                        `;
                            }
                        } else {
                            document.getElementById('probabilitiesSection').classList.add('d-none');
                        }
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    document.getElementById('hasilLoading').classList.add('d-none');
                    document.getElementById('hasilEmpty').classList.remove('d-none');
                    alert('Terjadi kesalahan saat memproses prediksi');
                });
        });
    </script>
@endpush