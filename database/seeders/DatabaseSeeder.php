<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use App\Models\Sparepart;
use App\Models\Motor;
use App\Models\TransaksiServis;
use App\Services\CARTPredictor;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // SEED PELANGGAN
        // ==========================================
        $pelangganData = [
            ['nama' => 'Ahmad Fadli', 'no_hp' => '081234567890', 'alamat' => 'Jl. Sudirman No. 45, Padang'],
            ['nama' => 'Budi Santoso', 'no_hp' => '082345678901', 'alamat' => 'Jl. Diponegoro No. 12, Padang'],
            ['nama' => 'Citra Dewi', 'no_hp' => '083456789012', 'alamat' => 'Jl. Veteran No. 88, Padang'],
            ['nama' => 'Deni Kurniawan', 'no_hp' => '084567890123', 'alamat' => 'Jl. Imam Bonjol No. 23, Padang'],
            ['nama' => 'Eka Putri', 'no_hp' => '085678901234', 'alamat' => 'Jl. Proklamasi No. 56, Padang'],
            ['nama' => 'Fajar Rahman', 'no_hp' => '086789012345', 'alamat' => 'Jl. Kartini No. 34, Padang'],
            ['nama' => 'Gita Maharani', 'no_hp' => '087890123456', 'alamat' => 'Jl. Pahlawan No. 67, Padang'],
            ['nama' => 'Hendra Wijaya', 'no_hp' => '088901234567', 'alamat' => 'Jl. Merdeka No. 89, Padang'],
        ];

        foreach ($pelangganData as $data) {
            Pelanggan::create($data);
        }

        echo "✓ Seeded 8 pelanggan\n";

        // ==========================================
        // SEED SPAREPART
        // ==========================================
        $sparepartData = [
            ['kode_part' => 'OLI-001', 'nama_barang' => 'Oli Mesin Yamalube Super Matic 1L', 'stok' => 50, 'harga_beli' => 45000, 'harga_jual' => 55000],
            ['kode_part' => 'OLI-002', 'nama_barang' => 'Oli Mesin Yamalube Silver 0.8L', 'stok' => 40, 'harga_beli' => 38000, 'harga_jual' => 48000],
            ['kode_part' => 'OLI-003', 'nama_barang' => 'Oli Gear Yamalube 100ml', 'stok' => 30, 'harga_beli' => 15000, 'harga_jual' => 22000],
            ['kode_part' => 'BUS-001', 'nama_barang' => 'Busi NGK Standard Yamaha', 'stok' => 25, 'harga_beli' => 18000, 'harga_jual' => 28000],
            ['kode_part' => 'BUS-002', 'nama_barang' => 'Busi Iridium Racing Yamaha', 'stok' => 15, 'harga_beli' => 75000, 'harga_jual' => 95000],
            ['kode_part' => 'KRM-001', 'nama_barang' => 'Kampas Rem Depan Yamaha Genuine', 'stok' => 20, 'harga_beli' => 35000, 'harga_jual' => 50000],
            ['kode_part' => 'KRM-002', 'nama_barang' => 'Kampas Rem Belakang Yamaha Genuine', 'stok' => 20, 'harga_beli' => 30000, 'harga_jual' => 45000],
            ['kode_part' => 'VBT-001', 'nama_barang' => 'V-Belt Kit Yamaha Mio', 'stok' => 10, 'harga_beli' => 85000, 'harga_jual' => 120000],
            ['kode_part' => 'VBT-002', 'nama_barang' => 'V-Belt Kit Yamaha NMAX', 'stok' => 12, 'harga_beli' => 95000, 'harga_jual' => 135000],
            ['kode_part' => 'ROL-001', 'nama_barang' => 'Roller Set Yamaha Mio', 'stok' => 8, 'harga_beli' => 45000, 'harga_jual' => 65000],
            ['kode_part' => 'ROL-002', 'nama_barang' => 'Roller Set Yamaha NMAX', 'stok' => 8, 'harga_beli' => 55000, 'harga_jual' => 75000],
            ['kode_part' => 'AKI-001', 'nama_barang' => 'Aki Kering GTZ5S GS YUASA', 'stok' => 6, 'harga_beli' => 150000, 'harga_jual' => 195000],
            ['kode_part' => 'AKI-002', 'nama_barang' => 'Aki Kering YTZ7S GS YUASA', 'stok' => 4, 'harga_beli' => 250000, 'harga_jual' => 320000],
            ['kode_part' => 'FLT-001', 'nama_barang' => 'Filter Udara Standar Yamaha', 'stok' => 15, 'harga_beli' => 25000, 'harga_jual' => 40000],
            ['kode_part' => 'BAN-001', 'nama_barang' => 'Ban Dalam Yamaha Genuine 17', 'stok' => 10, 'harga_beli' => 35000, 'harga_jual' => 50000],
            ['kode_part' => 'LMP-001', 'nama_barang' => 'Lampu Depan LED Yamaha Genuine', 'stok' => 10, 'harga_beli' => 55000, 'harga_jual' => 75000],
            ['kode_part' => 'RTG-001', 'nama_barang' => 'Rantai & Gear Set Yamaha MX King', 'stok' => 10, 'harga_beli' => 160000, 'harga_jual' => 210000],
            ['kode_part' => 'RTG-002', 'nama_barang' => 'Rantai & Gear Set Yamaha Vixion', 'stok' => 10, 'harga_beli' => 180000, 'harga_jual' => 235000],
        ];

        foreach ($sparepartData as $data) {
            Sparepart::create($data);
        }

        echo "✓ Seeded 18 sparepart\n";

        // ==========================================
        // SEED MOTOR
        // ==========================================
        $motorData = [
            ['id_pelanggan' => 1, 'no_polisi' => 'BA 1234 AB', 'merk' => 'Yamaha Mio M3', 'tipe_motor' => 'Matic', 'tahun_pembuatan' => 2020],
            ['id_pelanggan' => 1, 'no_polisi' => 'BA 5678 CD', 'merk' => 'Yamaha Vixion R', 'tipe_motor' => 'Sport', 'tahun_pembuatan' => 2018],
            ['id_pelanggan' => 2, 'no_polisi' => 'BA 2345 EF', 'merk' => 'Yamaha Aerox 155', 'tipe_motor' => 'Matic', 'tahun_pembuatan' => 2021],
            ['id_pelanggan' => 3, 'no_polisi' => 'BA 3456 GH', 'merk' => 'Yamaha NMAX 155', 'tipe_motor' => 'Matic', 'tahun_pembuatan' => 2022],
            ['id_pelanggan' => 4, 'no_polisi' => 'BA 4567 IJ', 'merk' => 'Yamaha Jupiter Z1', 'tipe_motor' => 'Bebek', 'tahun_pembuatan' => 2017],
            ['id_pelanggan' => 5, 'no_polisi' => 'BA 5678 KL', 'merk' => 'Yamaha Mio Soul GT', 'tipe_motor' => 'Matic', 'tahun_pembuatan' => 2019],
            ['id_pelanggan' => 6, 'no_polisi' => 'BA 6789 MN', 'merk' => 'Yamaha YZF-R15', 'tipe_motor' => 'Sport', 'tahun_pembuatan' => 2020],
            ['id_pelanggan' => 7, 'no_polisi' => 'BA 7890 OP', 'merk' => 'Yamaha Vega Force', 'tipe_motor' => 'Bebek', 'tahun_pembuatan' => 2016],
            ['id_pelanggan' => 8, 'no_polisi' => 'BA 8901 QR', 'merk' => 'Yamaha Lexi 125', 'tipe_motor' => 'Matic', 'tahun_pembuatan' => 2023],
        ];

        foreach ($motorData as $data) {
            Motor::create($data);
        }

        echo "✓ Seeded 9 motor\n";

        // ==========================================
        // SEED TRANSAKSI SERVIS
        // ==========================================
        $predictor = new CARTPredictor();
        $kategoriKeluhan = ['Rutin', 'Mesin', 'Pengereman', 'Kaki-kaki', 'Kelistrikan'];

        $transaksiData = [
            // Motor 1 - Yamaha Mio M3 (history)
            [
                'id_pelanggan' => 1,
                'id_motor' => 1,
                'tanggal_servis' => Carbon::now()->subMonths(3),
                'km_saat_ini' => 12000,
                'kategori_keluhan' => 'Rutin',
                'keluhan_konsumen' => 'Ganti oli rutin',
                'tindakan_mekanik' => 'Ganti oli mesin, cek filter udara',
                'biaya_jasa' => 25000,
                'status' => 'selesai',
            ],
            [
                'id_pelanggan' => 1,
                'id_motor' => 1,
                'tanggal_servis' => Carbon::now()->subDays(15),
                'km_saat_ini' => 15000,
                'kategori_keluhan' => 'Rutin',
                'keluhan_konsumen' => 'Servis berkala',
                'tindakan_mekanik' => 'Ganti oli, cek CVT',
                'biaya_jasa' => 35000,
                'status' => 'selesai',
            ],
            // Motor 3 - Yamaha Aerox 155
            [
                'id_pelanggan' => 2,
                'id_motor' => 3,
                'tanggal_servis' => Carbon::now()->subMonths(2),
                'km_saat_ini' => 8000,
                'kategori_keluhan' => 'CVT',
                'keluhan_konsumen' => 'Motor terasa berat',
                'tindakan_mekanik' => 'Ganti roller, v-belt masih bagus',
                'biaya_jasa' => 50000,
                'status' => 'selesai',
            ],
            // Motor 4 - Yamaha NMAX 155
            [
                'id_pelanggan' => 3,
                'id_motor' => 4,
                'tanggal_servis' => Carbon::now()->subDays(5),
                'km_saat_ini' => 5000,
                'kategori_keluhan' => 'Rutin',
                'keluhan_konsumen' => 'Ganti oli pertama',
                'tindakan_mekanik' => 'Ganti oli mesin dan oli gear',
                'biaya_jasa' => 30000,
                'status' => 'selesai',
            ],
            // Motor 5 - Yamaha Jupiter Z1
            [
                'id_pelanggan' => 4,
                'id_motor' => 5,
                'tanggal_servis' => Carbon::now()->subMonths(1),
                'km_saat_ini' => 45000,
                'kategori_keluhan' => 'Mesin',
                'keluhan_konsumen' => 'Mesin kurang bertenaga',
                'tindakan_mekanik' => 'Tune up, ganti busi, cek valve',
                'biaya_jasa' => 75000,
                'status' => 'selesai',
            ],
            // Motor 6 - Yamaha Mio Soul GT
            [
                'id_pelanggan' => 5,
                'id_motor' => 6,
                'tanggal_servis' => Carbon::now()->subDays(2),
                'km_saat_ini' => 22000,
                'kategori_keluhan' => 'Pengereman',
                'keluhan_konsumen' => 'Rem kurang pakem',
                'tindakan_mekanik' => 'Ganti kampas rem depan dan belakang',
                'biaya_jasa' => 35000,
                'status' => 'proses',
            ],
            // Motor 7 - Yamaha YZF-R15
            [
                'id_pelanggan' => 6,
                'id_motor' => 7,
                'tanggal_servis' => Carbon::now(),
                'km_saat_ini' => 18000,
                'kategori_keluhan' => 'Kelistrikan',
                'keluhan_konsumen' => 'Lampu sering mati',
                'tindakan_mekanik' => 'Cek kelistrikan, ganti relay',
                'biaya_jasa' => 45000,
                'status' => 'pending',
            ],
            // Motor 8 - Yamaha Vega Force
            [
                'id_pelanggan' => 7,
                'id_motor' => 8,
                'tanggal_servis' => Carbon::now()->subMonths(4),
                'km_saat_ini' => 55000,
                'kategori_keluhan' => 'Kaki-kaki',
                'keluhan_konsumen' => 'Shock depan keras',
                'tindakan_mekanik' => 'Ganti oli shock, cek bearing',
                'biaya_jasa' => 80000,
                'status' => 'selesai',
            ],
        ];

        $lastServisPerMotor = [];

        foreach ($transaksiData as $data) {
            $motor = Motor::find($data['id_motor']);
            $usiaMotor = Carbon::now()->year - $motor->tahun_pembuatan;

            // Get last service for this motor
            $tanggalServisTerakhir = $lastServisPerMotor[$data['id_motor']] ?? null;
            $durasiHari = 0;

            if ($tanggalServisTerakhir) {
                $durasiHari = Carbon::parse($tanggalServisTerakhir)->diffInDays(Carbon::parse($data['tanggal_servis']));
            }

            // Get prediction
            $kategori = $data['kategori_keluhan'] === 'CVT' ? 'Rutin' : $data['kategori_keluhan'];
            $prediksi = $predictor->predict(
                $durasiHari,
                $data['km_saat_ini'],
                $usiaMotor,
                $kategori,
                $motor->tipe_motor
            );

            TransaksiServis::create([
                'id_pelanggan' => $data['id_pelanggan'],
                'id_motor' => $data['id_motor'],
                'tanggal_servis' => $data['tanggal_servis'],
                'tanggal_servis_terakhir' => $tanggalServisTerakhir,
                'km_saat_ini' => $data['km_saat_ini'],
                'durasi_hari' => $durasiHari,
                'usia_motor' => $usiaMotor,
                'kategori_keluhan' => $kategori,
                'keluhan_konsumen' => $data['keluhan_konsumen'],
                'tindakan_mekanik' => $data['tindakan_mekanik'],
                'rekomendasi_servis' => $prediksi['rekomendasi'],
                'biaya_jasa' => $data['biaya_jasa'],
                'total_biaya' => $data['biaya_jasa'],
                'status' => $data['status'],
            ]);

            // Update last service for this motor
            $lastServisPerMotor[$data['id_motor']] = $data['tanggal_servis'];
        }

        echo "✓ Seeded 8 transaksi servis\n";
        echo "\n========================================\n";
        echo "Database seeding completed successfully!\n";
        echo "========================================\n";
    }
}
