<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CARTPredictor
{
    private string $apiUrl;
    private bool $useApi;

    public function __construct()
    {
        $this->apiUrl = env('CART_API_URL', 'http://localhost:5000');
        $this->useApi = env('CART_USE_API', true);
    }

    /**
     * @param int $durasiHari
     * @param int $kmTempuh
     * @param int $usiaMotor
     * @param string $kategoriKeluhan
     * @param string $tipeMotor
     * @return array
     */
    public function predict(
        int $durasiHari,
        int $kmTempuh,
        int $usiaMotor,
        string $kategoriKeluhan,
        string $tipeMotor
    ): array {
        $apiResult = $this->predictFromApi($durasiHari, $kmTempuh, $usiaMotor, $kategoriKeluhan, $tipeMotor);

        if ($apiResult !== null) {
            return $apiResult;
        }

        // Graceful Degradation Fallback
        Log::warning("CART API is down or failed. Returning graceful degradation fallback prediction.");
        return [
            'rekomendasi' => 'Servis Ringan',
            'confidence' => 0.00,
            'source' => 'fallback',
            'fallback_text' => 'Disarankan melakukan servis rutin berkala sesuai jadwal perawatan kendaraan Anda.',
            'probabilities' => [],
            'input' => [
                'durasi_hari' => $durasiHari,
                'km_tempuh' => $kmTempuh,
                'usia_motor' => $usiaMotor,
                'kategori_keluhan' => $kategoriKeluhan,
                'tipe_motor' => $tipeMotor,
            ]
        ];
    }

    private function predictFromApi(
        int $durasiHari,
        int $kmTempuh,
        int $usiaMotor,
        string $kategoriKeluhan,
        string $tipeMotor
    ): ?array {
        try {
            $response = Http::timeout(2)->post("{$this->apiUrl}/predict", [
                'durasi' => $durasiHari,
                'km' => $kmTempuh,
                'usia' => $usiaMotor,
                'tipe' => $tipeMotor,
                'keluhan' => $kategoriKeluhan,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    return [
                        'rekomendasi' => $data['prediction'],
                        'confidence' => $data['confidence'] ?? 0.85,
                        'source' => 'api',
                        'probabilities' => $data['probabilities'] ?? [],
                        'input' => [
                            'durasi_hari' => $durasiHari,
                            'km_tempuh' => $kmTempuh,
                            'usia_motor' => $usiaMotor,
                            'kategori_keluhan' => $kategoriKeluhan,
                            'tipe_motor' => $tipeMotor,
                        ]
                    ];
                }
            }

            Log::warning('CART API returned unsuccessful response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::warning('CART API not available, using fallback rules', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    public function isApiAvailable(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->apiUrl}/");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getModelInfo(): ?array
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/model-info");

            if ($response->successful()) {
                $data = $response->json();
                return $data['info'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get model info', ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function getCategories(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/categories");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    return [
                        'tipe' => $data['tipe'] ?? ['Matic', 'Bebek', 'Sport'],
                        'keluhan' => $data['keluhan'] ?? ['Ban', 'CVT', 'CVT & Kaki', 'Injeksi', 'Kaki-kaki', 'Kelistrikan', 'Kopling', 'Mesin', 'Pengereman', 'Performa', 'Radiator', 'Rantai', 'Rem', 'Rutin'],
                        'rekomendasi' => $data['rekomendasi'] ?? []
                    ];
                }
            }
        } catch (\Exception $e) {
        }

        throw new \Exception("Server Machine Learning (CART) tidak dapat dihubungi saat memuat data kategori. Pastikan program AI (api.py) berjalan!");
    }

    public static function getRekomendasiDescription(string $rekomendasi): string
    {
        return match ($rekomendasi) {
            'Servis Ringan' => 'Servis ringan: ganti oli, cek filter udara, tune-up standar',
            'Servis CVT' => 'Servis CVT: cek/ganti v-belt, roller, per CVT',
            'Servis Besar' => 'Servis besar: overhaul mesin, perbaikan komponen utama',
            'Ganti Part Fast Moving' => 'Ganti part fast moving: kampas rem, busi, aki, lampu, bearing',
            default => 'Konsultasi dengan mekanik untuk pemeriksaan lebih lanjut',
        };
    }

    public static function getKeluhanOptions(): array
    {
        return ['Ban', 'CVT', 'CVT & Kaki', 'Injeksi', 'Kaki-kaki', 'Kelistrikan', 'Kopling', 'Mesin', 'Pengereman', 'Performa', 'Radiator', 'Rantai', 'Rem', 'Rutin'];
    }

    public static function getTipeMotorOptions(): array
    {
        return ['Matic', 'Bebek', 'Sport'];
    }
    public static function getRekomendasiOptions(): array
    {
        return ['Servis Ringan', 'Servis CVT', 'Servis Besar', 'Ganti Part Fast Moving'];
    }
}
