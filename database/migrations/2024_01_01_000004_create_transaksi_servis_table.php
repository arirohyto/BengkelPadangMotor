<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaksi_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pelanggan')->constrained('pelanggan')->onDelete('cascade');
            $table->foreignId('id_motor')->constrained('motor')->onDelete('cascade');

            $table->date('tanggal_servis');
            $table->date('tanggal_servis_terakhir')->nullable();

            $table->integer('km_saat_ini');
            $table->integer('durasi_hari')->nullable();
            $table->integer('usia_motor')->nullable();
            $table->enum('kategori_keluhan', ['Rutin', 'Mesin', 'Pengereman', 'Kaki-kaki', 'Kelistrikan']);

            $table->text('keluhan_konsumen')->nullable();
            $table->text('tindakan_mekanik')->nullable();

            $table->enum('rekomendasi_servis', ['Ringan', 'CVT', 'Berat', 'Ganti Part Fast Moving'])->nullable();

            $table->decimal('biaya_jasa', 12, 2)->default(0);
            $table->decimal('total_biaya', 12, 2)->default(0);

            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_servis');
    }
};
