<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sparepart;

class SparepartKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            'busi' => 'Busi',
            'kampas rem' => 'Kampas Rem',
            'v-belt' => 'V-Belt',
            'roller' => 'Roller CVT',
            'filter udara' => 'Filter Udara',
            'aki' => 'Aki',
            'rantai' => 'Rantai Gear',
            'gir' => 'Rantai Gear',
            'gear' => 'Rantai Gear',
        ];

        $spareparts = Sparepart::all();
        $count = 0;

        foreach ($spareparts as $sparepart) {
            $nama = strtolower($sparepart->nama_barang);
            $matched = null;

            foreach ($mappings as $keyword => $kategori) {
                if (str_contains($nama, $keyword)) {
                    $matched = $kategori;
                    break;
                }
            }

            if ($matched) {
                $sparepart->update(['kategori_komponen' => $matched]);
                $count++;
            }
        }

        echo "✓ Successfully mapped {$count} spareparts to their kategori_komponen\n";
    }
}
