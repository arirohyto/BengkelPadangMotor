<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Motor;
use App\Models\TransaksiServis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredictionOverrideTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private Pelanggan $pelanggan;
    private Motor $maticMotor;
    private Motor $bebekMotor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::factory()->create([
            'username' => 'kasirtest',
            'role' => 'kasir'
        ]);

        $this->pelanggan = Pelanggan::create([
            'nama' => 'Jane Doe',
            'alamat' => 'Bukittinggi',
            'no_hp' => '081299998888'
        ]);

        $this->maticMotor = Motor::create([
            'id_pelanggan' => $this->pelanggan->id,
            'no_polisi' => 'BA 2222 CD',
            'merk' => 'Honda Beat',
            'tipe_motor' => 'Matic',
            'tahun_pembuatan' => 2021
        ]);

        $this->bebekMotor = Motor::create([
            'id_pelanggan' => $this->pelanggan->id,
            'no_polisi' => 'BA 3333 EF',
            'merk' => 'Honda Supra X',
            'tipe_motor' => 'Bebek',
            'tahun_pembuatan' => 2018
        ]);
    }

    public function test_store_transaction_with_servis_besar_for_matic_overrides_next_recommendation_to_servis_cvt()
    {
        $response = $this->actingAs($this->kasir)
            ->post(route('transaksi.store'), [
                'id_pelanggan' => $this->pelanggan->id,
                'id_motor' => $this->maticMotor->id,
                'tanggal_servis' => '2026-06-11',
                'km_saat_ini' => 12000,
                'kategori_keluhan' => 'Mesin',
                'jenis_servis' => 'Servis Besar',
                'biaya_jasa' => 100000,
                'keluhan_konsumen' => '',
                'tindakan_mekanik' => '',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('transaksi_servis', [
            'id_motor' => $this->maticMotor->id,
            'jenis_servis' => 'Servis Besar',
            'rekomendasi_servis' => 'Servis CVT'
        ]);

        $transaksi = TransaksiServis::where('id_motor', $this->maticMotor->id)->first();
        $this->assertTrue($transaksi->wasRekomendasiOverridden());
    }

    public function test_store_transaction_with_servis_besar_for_bebek_overrides_next_recommendation_to_servis_ringan()
    {
        $response = $this->actingAs($this->kasir)
            ->post(route('transaksi.store'), [
                'id_pelanggan' => $this->pelanggan->id,
                'id_motor' => $this->bebekMotor->id,
                'tanggal_servis' => '2026-06-11',
                'km_saat_ini' => 15000,
                'kategori_keluhan' => 'Mesin',
                'jenis_servis' => 'Servis Besar',
                'biaya_jasa' => 100000,
                'keluhan_konsumen' => '',
                'tindakan_mekanik' => '',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('transaksi_servis', [
            'id_motor' => $this->bebekMotor->id,
            'jenis_servis' => 'Servis Besar',
            'rekomendasi_servis' => 'Servis Ringan'
        ]);

        $transaksi = TransaksiServis::where('id_motor', $this->bebekMotor->id)->first();
        $this->assertTrue($transaksi->wasRekomendasiOverridden());
    }

    public function test_api_prediksi_with_servis_besar_for_matic_overrides_recommendation_to_servis_cvt()
    {
        $response = $this->actingAs($this->kasir)
            ->postJson(route('api.prediksi'), [
                'motor_id' => $this->maticMotor->id,
                'km_saat_ini' => 12000,
                'kategori_keluhan' => 'Mesin',
                'jenis_servis' => 'Servis Besar',
                'tanggal_servis' => '2026-06-11',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.rekomendasi', 'Servis CVT');
    }

    public function test_api_prediksi_with_servis_besar_for_bebek_overrides_recommendation_to_servis_ringan()
    {
        $response = $this->actingAs($this->kasir)
            ->postJson(route('api.prediksi'), [
                'motor_id' => $this->bebekMotor->id,
                'km_saat_ini' => 15000,
                'kategori_keluhan' => 'Mesin',
                'jenis_servis' => 'Servis Besar',
                'tanggal_servis' => '2026-06-11',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.rekomendasi', 'Servis Ringan');
    }

    public function test_cannot_access_nota_if_status_is_not_selesai()
    {
        $transaksi = TransaksiServis::create([
            'id_kasir' => $this->kasir->id,
            'id_pelanggan' => $this->pelanggan->id,
            'id_motor' => $this->maticMotor->id,
            'tanggal_servis' => '2026-06-11',
            'km_saat_ini' => 10000,
            'kategori_keluhan' => 'Rutin',
            'jenis_servis' => 'Servis Ringan',
            'rekomendasi_servis' => 'Servis Ringan',
            'biaya_jasa' => 25000,
            'total_biaya' => 25000,
            'status' => 'diterima'
        ]);

        $response = $this->actingAs($this->kasir)
            ->get(route('transaksi.nota', $transaksi));

        $response->assertRedirect(route('transaksi.show', $transaksi));
        $response->assertSessionHas('error');
    }

    public function test_can_access_nota_if_status_is_selesai()
    {
        $transaksi = TransaksiServis::create([
            'id_kasir' => $this->kasir->id,
            'id_pelanggan' => $this->pelanggan->id,
            'id_motor' => $this->maticMotor->id,
            'tanggal_servis' => '2026-06-11',
            'km_saat_ini' => 10000,
            'kategori_keluhan' => 'Rutin',
            'jenis_servis' => 'Servis Ringan',
            'rekomendasi_servis' => 'Servis Ringan',
            'biaya_jasa' => 25000,
            'total_biaya' => 25000,
            'status' => 'selesai'
        ]);

        $response = $this->actingAs($this->kasir)
            ->get(route('transaksi.nota', $transaksi));

        $response->assertStatus(200);
    }

    public function test_servis_cvt_today_recommends_ganti_part_fast_moving_next_if_kampas_rem_is_critical()
    {
        // Setup sparepart for Kampas Rem
        $kampasRem = \App\Models\Sparepart::create([
            'kode_part' => 'KRM-001',
            'nama_barang' => 'Kampas Rem',
            'harga_beli' => 20000,
            'harga_jual' => 45000,
            'stok' => 10,
            'kategori_komponen' => 'Kampas Rem'
        ]);

        $busi = \App\Models\Sparepart::create([
            'kode_part' => 'BSI-001',
            'nama_barang' => 'Busi Iridium',
            'harga_beli' => 25000,
            'harga_jual' => 50000,
            'stok' => 10,
            'kategori_komponen' => 'Busi'
        ]);

        $filterUdara = \App\Models\Sparepart::create([
            'kode_part' => 'FLT-001',
            'nama_barang' => 'Filter Udara',
            'harga_beli' => 15000,
            'harga_jual' => 35000,
            'stok' => 10,
            'kategori_komponen' => 'Filter Udara'
        ]);

        // Transaction #1: Kampas Rem, Busi, and Filter Udara replaced at 20,000 km
        $tx1 = TransaksiServis::create([
            'id_kasir' => $this->kasir->id,
            'id_pelanggan' => $this->pelanggan->id,
            'id_motor' => $this->maticMotor->id,
            'tanggal_servis' => '2026-06-11',
            'km_saat_ini' => 20000,
            'kategori_keluhan' => 'Rutin',
            'jenis_servis' => 'Ganti Part Fast Moving',
            'rekomendasi_servis' => 'Servis Ringan',
            'biaya_jasa' => 25000,
            'total_biaya' => 70000,
            'status' => 'selesai'
        ]);
        $tx1->spareparts()->attach($kampasRem->id, ['jumlah' => 1, 'harga_satuan' => 45000, 'subtotal' => 45000]);
        $tx1->spareparts()->attach($busi->id, ['jumlah' => 1, 'harga_satuan' => 50000, 'subtotal' => 50000]);
        $tx1->spareparts()->attach($filterUdara->id, ['jumlah' => 1, 'harga_satuan' => 35000, 'subtotal' => 35000]);

        // Transaction #2: Servis CVT at 30,000 km
        // At projected 33,000 km (V-Belt & Roller excluded because Servis CVT today):
        //   Busi: delta = 33,000 - 20,000 = 13,000 >= 8,000 → KRITIS
        //   Kampas Rem: delta = 33,000 - 20,000 = 13,000 >= 10,000 → KRITIS
        //   Filter Udara: delta = 33,000 - 20,000 = 13,000 < 16,000 → AMAN
        //   Aki: delta = 33,000 - 0 = 33,000 >= 24,000 → KRITIS
        // kritis = 3 (Busi, Kampas Rem, Aki), totalEvaluated = 4 → 3 < 4, no escalation
        // hasCVT = false → override to 'Ganti Part Fast Moving'
        $response = $this->actingAs($this->kasir)
            ->post(route('transaksi.store'), [
                'id_pelanggan' => $this->pelanggan->id,
                'id_motor' => $this->maticMotor->id,
                'tanggal_servis' => '2026-09-11',
                'km_saat_ini' => 30000,
                'kategori_keluhan' => 'CVT',
                'jenis_servis' => 'Servis CVT',
                'biaya_jasa' => 30000,
                'keluhan_konsumen' => '',
                'tindakan_mekanik' => '',
            ]);

        $response->assertRedirect();

        // Verify that the stored transaction has recommendation 'Ganti Part Fast Moving'
        $this->assertDatabaseHas('transaksi_servis', [
            'id_motor' => $this->maticMotor->id,
            'km_saat_ini' => 30000,
            'jenis_servis' => 'Servis CVT',
            'rekomendasi_servis' => 'Ganti Part Fast Moving'
        ]);
    }

    public function test_get_kritis_components_returns_cvt_components_under_servis_cvt_recommendation()
    {
        // Setup a transaction where recommendation is Servis CVT and CVT components are critical
        $transaksi = TransaksiServis::create([
            'id_kasir' => $this->kasir->id,
            'id_pelanggan' => $this->pelanggan->id,
            'id_motor' => $this->maticMotor->id,
            'tanggal_servis' => '2026-06-11',
            'km_saat_ini' => 25000,
            'kategori_keluhan' => 'Rutin',
            'jenis_servis' => 'Servis Ringan',
            'rekomendasi_servis' => 'Servis CVT',
            'biaya_jasa' => 25000,
            'total_biaya' => 25000,
            'status' => 'selesai'
        ]);

        $kritis = $transaksi->kritis_components;

        // At KM 25,000 + 3000 = 28,000 projected KM:
        // V-Belt delta is 28,000 (limit 20,000) -> Kritis!
        // Roller CVT delta is 28,000 (limit 20,000) -> Kritis!
        $this->assertNotEmpty($kritis);
        $kompNames = collect($kritis)->pluck('nama')->toArray();
        $this->assertContains('V-Belt', $kompNames);
        $this->assertContains('Roller CVT', $kompNames);
    }
}
