<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE transaksi_servis MODIFY kategori_keluhan VARCHAR(255) NOT NULL");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE transaksi_servis MODIFY rekomendasi_servis VARCHAR(255) NULL");
        }
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE transaksi_servis MODIFY kategori_keluhan ENUM('Rutin', 'Mesin', 'Pengereman', 'Kaki-kaki', 'Kelistrikan') NOT NULL");
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE transaksi_servis MODIFY rekomendasi_servis ENUM('Ringan', 'CVT', 'Berat', 'Ganti Part Fast Moving') NULL");
        }
    }
};
