<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TransaksiServis;
use App\Models\Pelanggan;
use App\Models\Motor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_laporan_index_or_detail()
    {
        $this->get(route('laporan.index'))
            ->assertRedirect(route('login'));

        $this->get(route('laporan.detail', ['tahun' => 2026, 'bulan' => 3]))
            ->assertRedirect(route('login'));
    }

    public function test_kasir_cannot_access_laporan_index_or_detail()
    {
        $kasir = User::factory()->create([
            'username' => 'kasir1',
            'role' => 'kasir'
        ]);

        $this->actingAs($kasir)
            ->get(route('laporan.index'))
            ->assertStatus(403);

        $this->actingAs($kasir)
            ->get(route('laporan.detail', ['tahun' => 2026, 'bulan' => 3]))
            ->assertStatus(403);
    }

    public function test_pemilik_can_access_laporan_index_and_detail()
    {
        app()->setLocale('id');
        \Carbon\Carbon::setLocale('id');

        $pemilik = User::factory()->create([
            'username' => 'pemilik1',
            'name' => 'pemilik1',
            'role' => 'pemilik'
        ]);

        // Create data to show in report
        $pelanggan = Pelanggan::create([
            'nama' => 'John Doe',
            'alamat' => 'Padang',
            'no_hp' => '081234567890'
        ]);

        $motor = Motor::create([
            'id_pelanggan' => $pelanggan->id,
            'no_polisi' => 'BA 1234 AB',
            'merk' => 'Yamaha Mio',
            'tipe_motor' => 'Matic',
            'tahun_pembuatan' => 2020
        ]);

        TransaksiServis::create([
            'id_kasir' => $pemilik->id,
            'id_pelanggan' => $pelanggan->id,
            'id_motor' => $motor->id,
            'tanggal_servis' => '2026-03-15',
            'km_saat_ini' => 15000,
            'kategori_keluhan' => 'Rutin',
            'jenis_servis' => 'Servis Ringan',
            'rekomendasi_servis' => 'Servis Ringan',
            'biaya_jasa' => 25000,
            'total_biaya' => 25000,
            'status' => 'selesai'
        ]);

        TransaksiServis::create([
            'id_kasir' => $pemilik->id,
            'id_pelanggan' => $pelanggan->id,
            'id_motor' => $motor->id,
            'tanggal_servis' => '2026-03-20',
            'km_saat_ini' => 15500,
            'kategori_keluhan' => 'CVT',
            'jenis_servis' => 'Servis CVT',
            'rekomendasi_servis' => 'Servis CVT',
            'biaya_jasa' => 50000,
            'total_biaya' => 50000,
            'status' => 'dikerjakan'
        ]);

        $responseIndex = $this->actingAs($pemilik)->get(route('laporan.index', ['tahun' => 2026]));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Maret');
        $responseIndex->assertSee('2');
        $responseIndex->assertSee('Rp 25.000'); 

        $responseDetail = $this->actingAs($pemilik)->get(route('laporan.detail', ['tahun' => 2026, 'bulan' => 3]));
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('John Doe');
        $responseDetail->assertSee('Yamaha Mio');
        $responseDetail->assertSee('BA 1234 AB');
        $responseDetail->assertSee('Dikerjakan'); 
        $responseDetail->assertSee('Selesai'); 
        $responseDetail->assertSee('Rp 25.000');    
        $responseDetail->assertSee('pemilik1'); 
    }
}
