<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('motor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pelanggan')->constrained('pelanggan')->onDelete('cascade');
            $table->string('no_polisi', 15)->unique();
            $table->string('merk', 50); // Honda, Yamaha, Suzuki, Kawasaki, dll
            $table->enum('tipe_motor', ['Matic', 'Bebek', 'Sport']);
            $table->year('tahun_pembuatan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motor');
    }
};
