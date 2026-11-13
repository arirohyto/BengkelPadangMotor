<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Motor;
use App\Models\Pelanggan;
use App\Models\TransaksiServis;
use App\Models\Sparepart;
use App\Http\Controllers\PrediksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    $pelanggan = Pelanggan::create([
        'nama' => 'Budi',
        'alamat' => 'Padang',
        'no_hp' => '08312317'
    ]);

    $motor = Motor::create([
        'id_pelanggan' => $pelanggan->id,
        'no_polisi' => 'BB 1 TEST3',
        'merk' => 'XMAX',
        'tipe_motor' => 'Matic',
        'tahun_pembuatan' => 2025
    ]);

    $busi = Sparepart::where('kategori_komponen', 'Busi')->first() ?? Sparepart::create(['kode_barang' => 'BUS-01', 'nama_barang' => 'Busi', 'harga_beli' => 10000, 'harga_jual' => 15000, 'stok' => 10, 'kategori_komponen' => 'Busi']);
    $rem = Sparepart::where('kategori_komponen', 'Kampas Rem')->first() ?? Sparepart::create(['kode_barang' => 'KRM-002', 'nama_barang' => 'Kampas Rem', 'harga_beli' => 20000, 'harga_jual' => 45000, 'stok' => 10, 'kategori_komponen' => 'Kampas Rem']);
    $filter = Sparepart::where('kategori_komponen', 'Filter Udara')->first() ?? Sparepart::create(['kode_barang' => 'FLT-01', 'nama_barang' => 'Filter', 'harga_beli' => 10000, 'harga_jual' => 15000, 'stok' => 10, 'kategori_komponen' => 'Filter Udara']);
    $aki = Sparepart::where('kategori_komponen', 'Aki')->first() ?? Sparepart::create(['kode_barang' => 'AKI-01', 'nama_barang' => 'Aki', 'harga_beli' => 100000, 'harga_jual' => 120000, 'stok' => 10, 'kategori_komponen' => 'Aki']);
    $vbelt = Sparepart::where('kategori_komponen', 'V-Belt')->first() ?? Sparepart::create(['kode_barang' => 'VBT-01', 'nama_barang' => 'V-Belt', 'harga_beli' => 50000, 'harga_jual' => 70000, 'stok' => 10, 'kategori_komponen' => 'V-Belt']);

    // 1. Transaction #44 at 20,000 km replacing Busi, Rem, Filter, Aki, V-Belt (so all are clean)
    $tx44 = TransaksiServis::create([
        'id_kasir' => 1,
        'id_pelanggan' => $pelanggan->id,
        'id_motor' => $motor->id,
        'tanggal_servis' => '2026-06-11',
        'km_saat_ini' => 20000,
        'kategori_keluhan' => 'Rutin',
        'jenis_servis' => 'Ganti Part Fast Moving',
        'rekomendasi_servis' => 'Servis CVT',
        'biaya_jasa' => 25000,
        'total_biaya' => 25000 + 15000 + 45000 + 15000 + 120000 + 70000,
        'status' => 'selesai'
    ]);
    $tx44->spareparts()->attach([
        $busi->id => ['jumlah' => 1, 'harga_satuan' => 15000, 'subtotal' => 15000],
        $rem->id => ['jumlah' => 1, 'harga_satuan' => 45000, 'subtotal' => 45000],
        $filter->id => ['jumlah' => 1, 'harga_satuan' => 15000, 'subtotal' => 15000],
        $aki->id => ['jumlah' => 1, 'harga_satuan' => 120000, 'subtotal' => 120000],
        $vbelt->id => ['jumlah' => 1, 'harga_satuan' => 70000, 'subtotal' => 70000]
    ]);

    // 2. Transaction #45 at 23,500 km (Servis CVT)
    $tx45 = TransaksiServis::create([
        'id_kasir' => 1,
        'id_pelanggan' => $pelanggan->id,
        'id_motor' => $motor->id,
        'tanggal_servis' => '2026-09-11',
        'km_saat_ini' => 23500,
        'kategori_keluhan' => 'Rutin',
        'jenis_servis' => 'Servis CVT',
        'rekomendasi_servis' => 'Servis CVT',
        'biaya_jasa' => 25000,
        'total_biaya' => 25000,
        'status' => 'selesai'
    ]);

    // 3. Transaction #47 at 26,500 km (Servis Besar) - replacing Busi, Filter, Aki (but NOT Kampas Rem)
    $tx47 = TransaksiServis::create([
        'id_kasir' => 1,
        'id_pelanggan' => $pelanggan->id,
        'id_motor' => $motor->id,
        'tanggal_servis' => '2026-11-11',
        'km_saat_ini' => 26500,
        'kategori_keluhan' => 'CVT',
        'jenis_servis' => 'Servis Besar',
        'rekomendasi_servis' => 'Servis CVT',
        'biaya_jasa' => 45000,
        'total_biaya' => 45000 + 15000 + 15000 + 120000,
        'status' => 'selesai'
    ]);
    $tx47->spareparts()->attach([
        $busi->id => ['jumlah' => 1, 'harga_satuan' => 15000, 'subtotal' => 15000],
        $filter->id => ['jumlah' => 1, 'harga_satuan' => 15000, 'subtotal' => 15000],
        $aki->id => ['jumlah' => 1, 'harga_satuan' => 120000, 'subtotal' => 120000]
    ]);

    // 4. Transaction #50 at 29,500 km (Servis CVT)
    $tx50 = TransaksiServis::create([
        'id_kasir' => 1,
        'id_pelanggan' => $pelanggan->id,
        'id_motor' => $motor->id,
        'tanggal_servis' => '2027-02-11',
        'km_saat_ini' => 29500,
        'kategori_keluhan' => 'Mesin',
        'jenis_servis' => 'Servis CVT',
        'rekomendasi_servis' => 'Servis CVT',
        'biaya_jasa' => 45000,
        'total_biaya' => 45000,
        'status' => 'selesai'
    ]);

    // 5. Today's transaction simulation at 32,500 km (Servis CVT)
    echo "Running prediction simulation today at 32.500 km...\n";
    $req = new Request([
        'motor_id' => $motor->id,
        'km_saat_ini' => 32500,
        'kategori_keluhan' => 'Rutin',
        'jenis_servis' => 'Servis CVT',
        'tanggal_servis' => '2027-05-11',
    ]);

    $ctrl = new PrediksiController();
    $res = $ctrl->predict($req);

    echo json_encode($res->getData(), JSON_PRETTY_PRINT) . "\n";

} finally {
    DB::rollBack();
    echo "Transaction rolled back.\n";
}
