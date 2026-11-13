<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            DB::statement("ALTER TABLE transaksi_servis MODIFY COLUMN status ENUM('pending','proses','selesai','diterima','dikerjakan') NOT NULL DEFAULT 'diterima'");
        }

        DB::table('transaksi_servis')
            ->where('status', 'pending')
            ->update(['status' => 'diterima']);

        DB::table('transaksi_servis')
            ->where('status', 'proses')
            ->update(['status' => 'dikerjakan']);

        if ($isMysql) {
            DB::statement("ALTER TABLE transaksi_servis MODIFY COLUMN status ENUM('diterima','dikerjakan','selesai') NOT NULL DEFAULT 'diterima'");
        }
    }
    
    public function down(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        if ($isMysql) {
            DB::statement("ALTER TABLE transaksi_servis MODIFY COLUMN status ENUM('diterima','dikerjakan','selesai','pending','proses') NOT NULL DEFAULT 'pending'");
        }

        DB::table('transaksi_servis')
            ->where('status', 'diterima')
            ->update(['status' => 'pending']);

        DB::table('transaksi_servis')
            ->where('status', 'dikerjakan')
            ->update(['status' => 'proses']);

        if ($isMysql) {
            DB::statement("ALTER TABLE transaksi_servis MODIFY COLUMN status ENUM('pending','proses','selesai') NOT NULL DEFAULT 'pending'");
        }
    }
};
