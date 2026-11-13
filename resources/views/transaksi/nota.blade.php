<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Servis #{{ $transaksi->id }} - Bengkel Padang Motor</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: #0b5ed7;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --text-color: #334155;
            --text-muted: #64748b;
            --red-color: #f80e0eff;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            background-color: #fff;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        }

        /* Header Style */
        .nota-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-section h1 {
            color: var(--dark-color);
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
        }

        .logo-section p {
            margin: 0;
            color: var(--text-muted);
            font-size: 12px;
        }

        .invoice-section {
            text-align: right;
        }

        .invoice-section h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: var(--dark-color);
        }

        .invoice-section p {
            margin: 0 0 3px 0;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Grid Info */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-card {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .info-card h3 {
            margin: 0 0 12px 0;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 13px;
        }

        .info-table td.label {
            color: var(--text-muted);
            width: 110px;
        }

        .info-table td.value {
            font-weight: 500;
            color: var(--dark-color);
        }

        /* Tables */
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table-items th {
            background-color: var(--dark-color);
            color: #fff;
            text-align: left;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }

        .table-items td {
            padding: 12px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }

        .table-items tr:last-child td {
            border-bottom: 2px solid var(--dark-color);
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totals */
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .total-table {
            width: 300px;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 6px 12px;
            font-size: 13px;
        }

        .total-table tr.grand-total td {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary-color);
            border-top: 1px solid var(--border-color);
            background-color: var(--light-color);
            padding: 10px 12px;
        }

        /* Kotak Rekomendasi Preventif */
        .rekomendasi-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1.5px solid #bfdbfe;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .rekomendasi-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background-color: var(--primary-color);
        }

        .rekomendasi-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .rekomendasi-header i {
            font-size: 20px;
            color: var(--primary-color);
            margin-right: 10px;
        }

        .rekomendasi-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary-color);
            margin: 0;
        }

        .rekomendasi-badge {
            background-color: var(--red-color);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 13px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .rekomendasi-box p {
            margin: 0 0 12px 0;
            font-size: 13px;
            color: #1e3a8a;
        }

        .komponen-list {
            margin: 0;
            padding-left: 20px;
            font-size: 12.5px;
            color: #f74747ff;
        }

        .komponen-list li {
            margin-bottom: 6px;
        }

        .rekomendasi-footer {
            margin-top: 15px;
            border-top: 1px dashed #bfdbfe;
            padding-top: 12px;
            font-size: 11px;
            color: #3b82f6;
            font-style: italic;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: var(--text-muted);
            font-size: 11px;
            margin-top: 40px;
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        /* Buttons Section (No Print) */
        .no-print-section {
            max-width: 800px;
            margin: 20px auto 0 auto;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0d6efd;
        }

        .btn-secondary {
            background-color: #64748b;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        @media print {
            body {
                padding: 0;
                background-color: #fff;
            }
            .container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="nota-header">
        <div class="logo-section">
            <h1>PADANG <span>MOTOR</span></h1>
            <p><strong>Yamaha Motor Indonesia Dealer</strong></p>
            <p>Jalan Ahmad Yani No. 209 A-B, 211 A-B, Batang Beruh, Kec. Sidikalang, Kabupaten Dairi, Sumatera Utara 22212 
            <p>Telp: 0823-6826-3854</p>
        </div>
        <div class="invoice-section">
            <h2>NOTA TRANSAKSI</h2>
            <p><strong>No. Nota:</strong> #TRX-{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Tanggal:</strong> {{ $transaksi->tanggal_servis->format('d M Y') }}</p>
            <p><strong>Kasir:</strong> {{ $transaksi->kasir->name ?? 'Kasir' }}</p>
            <p><strong>Status:</strong> LUNAS</p>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="info-grid">
        <div class="info-card">
            <h3>Pelanggan</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="value">{{ $transaksi->pelanggan->nama }}</td>
                </tr>
                <tr>
                    <td class="label">No. HP</td>
                    <td class="value">{{ $transaksi->pelanggan->no_hp }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="value">{{ $transaksi->pelanggan->alamat ?: '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="info-card">
            <h3>Kendaraan</h3>
            <table class="info-table">
                <tr>
                    <td class="label">No. Polisi</td>
                    <td class="value">{{ $transaksi->motor->no_polisi }}</td>
                </tr>
                <tr>
                    <td class="label">Model</td>
                    <td class="value">{{ $transaksi->motor->merk }}</td>
                </tr>
                <tr>
                    <td class="label">Tipe / Usia</td>
                    <td class="value">{{ $transaksi->motor->tipe_motor }} / {{ $transaksi->usia_motor }} Tahun</td>
                </tr>
                <tr>
                    <td class="label">KM Saat Ini</td>
                    <td class="value">{{ number_format($transaksi->km_saat_ini) }} KM</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Detail Servis Utama -->
    <table class="table-items">
        <thead>
            <tr>
                <th width="50%">Deskripsi Pekerjaan Servis</th>
                <th width="20%">Keluhan Pelanggan</th>
                <th width="30%" class="text-right">Biaya Jasa</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $transaksi->jenis_servis ?: 'Servis Berkala' }}</strong><br>
                    <small style="color: var(--text-muted);">Tindakan: {{ $transaksi->tindakan_mekanik ?: 'Pengecekan standar dan tune-up' }}</small>
                </td>
                <td>{{ $transaksi->kategori_keluhan }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->biaya_jasa, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Detail Spareparts (Jika Ada) -->
    @if($transaksi->spareparts->count() > 0)
        <h3 style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--dark-color); margin-bottom: 10px;">
            Suku Cadang (Yamaha Genuine Parts)
        </h3>
        <table class="table-items">
            <thead>
                <tr>
                    <th width="20%">Kode Part</th>
                    <th width="45%">Nama Barang</th>
                    <th width="15%" class="text-right">Harga</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="10%" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->spareparts as $sp)
                    <tr>
                        <td style="font-family: monospace;">{{ $sp->kode_part }}</td>
                        <td>{{ $sp->nama_barang }}</td>
                        <td class="text-right">Rp {{ number_format($sp->pivot->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $sp->pivot->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($sp->pivot->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Biaya Total -->
    <div class="total-section">
        <table class="total-table">
            <tr>
                <td class="text-muted">Total Jasa</td>
                <td class="text-right">Rp {{ number_format($transaksi->biaya_jasa, 0, ',', '.') }}</td>
            </tr>
            @if($transaksi->spareparts->count() > 0)
                <tr>
                    <td class="text-muted">Total Suku Cadang</td>
                    <td class="text-right">Rp {{ number_format($transaksi->total_sparepart, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ $transaksi->total_biaya_formatted }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Kotak Rekomendasi Preventif Terintegrasi CART -->
    @php
        // Calculate projected metrics
        $projectedKm = $transaksi->km_saat_ini + 3000;
        $projectedDurasi = 90;
        if ($transaksi->jenis_servis === 'Ganti Part Fast Moving') {
            $lastRoutine = \App\Models\TransaksiServis::where('id_motor', $transaksi->id_motor)
                ->where('id', '<', $transaksi->id)
                ->whereIn('jenis_servis', ['Servis Ringan', 'Servis CVT', 'Servis Besar'])
                ->latest('tanggal_servis')
                ->first();
            if ($lastRoutine) {
                $days = Carbon\Carbon::parse($lastRoutine->tanggal_servis)->diffInDays(Carbon\Carbon::parse($transaksi->tanggal_servis));
                if ($days < 90) {
                    $projectedDurasi = 90 - $days;
                }
            }
        }
    @endphp

    <div class="rekomendasi-box">
        <div class="rekomendasi-header">
            <h4 class="rekomendasi-title">Servis Selanjutnya Kendaraan Anda</h4>
        </div>
        
        <small style="color: #2563eb; display: block; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; font-size: 11px;">
            Saran Kunjungan Berikutnya (Estimasi +3.000 KM atau {{ ceil($projectedDurasi / 30) }} Bulan)
        </small>
        
        @if(in_array($transaksi->rekomendasi_servis, ['Ganti Part Fast Moving', 'Servis CVT']) && !empty($transaksi->kritis_components))
            <div class="rekomendasi-badge">
                Rekomendasi Berikutnya: {{ $transaksi->rekomendasi_servis }}
            </div>
            
            @php
                $nama_kritis = array_map(function($komp) {
                    return $komp['nama'];
                }, $transaksi->kritis_components);
                $nama_kritis_str = implode(', ', $nama_kritis);
            @endphp
            
            @if($transaksi->rekomendasi_servis === 'Servis CVT')
                <p>Komponen yang melewati batas pakai: <strong>{{ $nama_kritis_str }}</strong>. Disarankan melakukan Servis CVT untuk melakukan pemeriksaan dan penggantian komponen tersebut pada kunjungan berikutnya.</p>
            @else
                <p>Komponen yang melewati batas pakai: <strong>{{ $nama_kritis_str }}</strong>. Disarankan untuk melakukan pemeriksaan dan penggantian pada kunjungan berikutnya.</p>
            @endif
            
            <ul class="komponen-list">
                @foreach($transaksi->kritis_components as $komp)
                    <li>
                        <strong>{{ $komp['nama'] }}</strong>
                    </li>
                @endforeach
            </ul>
        @elseif($transaksi->wasRekomendasiOverridden())
            <div class="rekomendasi-badge">
                Rekomendasi Berikutnya: {{ $transaksi->rekomendasi_servis }}
            </div>
            
            @if($transaksi->rekomendasi_servis === 'Servis CVT')
                <p>Seluruh komponen fast moving diprediksikan masih aman karena baru saja diganti. Disarankan melakukan servis CVT rutin pada kunjungan berikutnya.</p>
            @else
                <p>Seluruh komponen fast moving diprediksikan masih aman karena baru saja diganti. Lakukan pengecekan rutin berkala seperti biasa pada kunjungan berikutnya.</p>
            @endif
        @else
            <div class="rekomendasi-badge">
                Rekomendasi Berikutnya: {{ $transaksi->rekomendasi_servis }}
            </div>
            <p>Disarankan melakukan {{ $transaksi->rekomendasi_servis }} sesuai dengan pola interval kendaraan Anda.</p>
        @endif
        
    </div>

    <!-- Tanda Tangan -->
    <div style="display: flex; justify-content: center; margin-top: 50px;">
        <div style="text-align: center; width: 200px;">
            <p style="margin-bottom: 60px;">Hormat Kami,</p>
            <p style="border-top: 1px solid var(--text-color); padding-top: 5px; font-weight: 600;">( {{ $transaksi->kasir->name ?? 'Kasir Padang Motor' }} )</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda mempercayakan perawatan motor Yamaha Anda di Bengkel Padang Motor.</p>
    </div>
</div>

<!-- Buttons Section (No Print) -->
<div class="no-print-section no-print">
    <a href="{{ route('transaksi.show', $transaksi) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="bi bi-printer me-1"></i> Cetak Nota Servis
    </button>
</div>

</body>
</html>
