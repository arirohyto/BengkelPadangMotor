<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class TransaksiServis extends Model
{
    use HasFactory;

    protected $table = 'transaksi_servis';

    protected $fillable = [
        'id_kasir',
        'id_pelanggan',
        'id_motor',
        'tanggal_servis',
        'tanggal_servis_terakhir',
        'km_saat_ini',
        'durasi_hari',
        'usia_motor',
        'kategori_keluhan',
        'keluhan_konsumen',
        'tindakan_mekanik',
        'jenis_servis',
        'rekomendasi_servis',
        'biaya_jasa',
        'total_biaya',
        'status',
    ];

    protected $casts = [
        'tanggal_servis' => 'date',
        'tanggal_servis_terakhir' => 'date',
        'biaya_jasa' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public const KATEGORI_KELUHAN = ['Ban', 'CVT', 'CVT & Kaki', 'Injeksi', 'Kaki-kaki', 'Kelistrikan', 'Kopling', 'Mesin', 'Pengereman', 'Performa', 'Radiator', 'Rantai', 'Rem', 'Rutin'];

    public const REKOMENDASI_SERVIS = ['Servis Ringan', 'Servis CVT', 'Servis Besar', 'Ganti Part Fast Moving'];

    public const JENIS_SERVIS = ['Servis Ringan', 'Servis CVT', 'Servis Besar', 'Ganti Part Fast Moving'];

    public const STATUS = ['diterima', 'dikerjakan', 'selesai'];

    public const KSB_LIFECYCLE = [
        'Busi' => 8000,
        'Kampas Rem' => 10000,
        'V-Belt' => 20000,
        'Roller CVT' => 20000,
        'Filter Udara' => 16000,
        'Aki' => 24000,
        'Rantai Gear' => 30000,
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function motor(): BelongsTo
    {
        return $this->belongsTo(Motor::class, 'id_motor');
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_kasir');
    }

    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(Sparepart::class, 'detail_sparepart_transaksi', 'id_transaksi', 'id_sparepart')
            ->withPivot('jumlah', 'harga_satuan', 'subtotal')
            ->withTimestamps();
    }

    public function calculateDurasiHari(): int
    {
        if ($this->tanggal_servis_terakhir) {
            $lastDate = Carbon::parse($this->tanggal_servis_terakhir);
            $currentDate = Carbon::parse($this->tanggal_servis);
            return $lastDate->diffInDays($currentDate);
        }
        return 0;
    }

    public function getTotalSparepartAttribute(): float
    {
        return $this->spareparts->sum('pivot.subtotal');
    }

    public function getTotalBiayaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'diterima' => 'warning',
            'dikerjakan' => 'info',
            'selesai' => 'success',
            default => 'secondary',
        };
    }

    public function getRekomendasiBadgeAttribute(): string
    {
        return match ($this->rekomendasi_servis) {
            'Ringan', 'Servis Ringan' => 'success',
            'CVT', 'Servis CVT' => 'info',
            'Berat', 'Servis Besar' => 'warning',
            'Ganti Part Fast Moving' => 'danger',
            default => 'secondary',
        };
    }

    public function getKritisComponentsAttribute(): array
    {
        if (!in_array($this->rekomendasi_servis, ['Ganti Part Fast Moving', 'Servis CVT', 'Servis Besar'])) {
            return [];
        }

        $motor = $this->motor;
        if (!$motor) {
            return [];
        }
        
        $projectedKm = $this->km_saat_ini + 3000;

        $replacementHistory = \DB::table('detail_sparepart_transaksi')
            ->join('transaksi_servis', 'detail_sparepart_transaksi.id_transaksi', '=', 'transaksi_servis.id')
            ->join('spareparts', 'detail_sparepart_transaksi.id_sparepart', '=', 'spareparts.id')
            ->where('transaksi_servis.id_motor', $this->id_motor)
            ->where('transaksi_servis.tanggal_servis', '<=', $this->tanggal_servis)
            ->whereNotNull('spareparts.kategori_komponen')
            ->select('spareparts.kategori_komponen', \DB::raw('MAX(transaksi_servis.km_saat_ini) as max_km'))
            ->groupBy('spareparts.kategori_komponen')
            ->pluck('max_km', 'kategori_komponen')
            ->toArray();

        $replacedToday = $this->spareparts()
            ->whereNotNull('spareparts.kategori_komponen')
            ->pluck('spareparts.kategori_komponen')
            ->toArray();

        if ($this->jenis_servis === 'Servis CVT') {
            $replacedToday = array_unique(array_merge($replacedToday, ['V-Belt', 'Roller CVT']));
        }


        $kritis = [];
        foreach (self::KSB_LIFECYCLE as $komponen => $limitKm) {
            if ($komponen === 'V-Belt' || $komponen === 'Roller CVT') {
                if ($motor->tipe_motor !== 'Matic') {
                    continue;
                }
            }
            if ($komponen === 'Rantai Gear') {
                if ($motor->tipe_motor === 'Matic') {
                    continue;
                }
            }

            if (in_array($komponen, $replacedToday)) {
                continue;
            }
            $lastKm = $replacementHistory[$komponen] ?? 0;
            $deltaKm = $projectedKm - $lastKm;

            if ($deltaKm >= $limitKm) {
                $kritis[] = [
                    'nama' => $komponen,
                    'delta_km' => $deltaKm,
                    'limit_km' => $limitKm,
                    'last_replaced_km' => $lastKm,
                ];
            }
        }

        return $kritis;
    }

    public function wasRekomendasiOverridden(): bool
    {
        if ($this->jenis_servis === 'Servis Besar') {
            return true;
        }

        $replacedToday = $this->spareparts()
            ->whereNotNull('spareparts.kategori_komponen')
            ->pluck('spareparts.kategori_komponen')
            ->toArray();

        if ($this->jenis_servis === 'Servis CVT') {
            $replacedToday = array_unique(array_merge($replacedToday, ['V-Belt', 'Roller CVT']));
        }


        if (empty($replacedToday)) {
            return false;
        }

        $motor = $this->motor;
        if (!$motor) {
            return false;
        }

        $projectedKm = $this->km_saat_ini + 3000;

        $replacementHistory = \DB::table('detail_sparepart_transaksi')
            ->join('transaksi_servis', 'detail_sparepart_transaksi.id_transaksi', '=', 'transaksi_servis.id')
            ->join('spareparts', 'detail_sparepart_transaksi.id_sparepart', '=', 'spareparts.id')
            ->where('transaksi_servis.id_motor', $this->id_motor)
            ->where('transaksi_servis.id', '<', $this->id)
            ->whereNotNull('spareparts.kategori_komponen')
            ->select('spareparts.kategori_komponen', \DB::raw('MAX(transaksi_servis.km_saat_ini) as max_km'))
            ->groupBy('spareparts.kategori_komponen')
            ->pluck('max_km', 'kategori_komponen')
            ->toArray();

        foreach ($replacedToday as $komponen) {
            if (!isset(self::KSB_LIFECYCLE[$komponen])) {
                continue;
            }
            $limitKm = self::KSB_LIFECYCLE[$komponen];
            
            if ($komponen === 'V-Belt' || $komponen === 'Roller CVT') {
                if ($motor->tipe_motor !== 'Matic') {
                    continue;
                }
            }
            if ($komponen === 'Rantai Gear') {
                if ($motor->tipe_motor === 'Matic') {
                    continue;
                }
            }

            $lastKm = $replacementHistory[$komponen] ?? 0;
            $deltaKm = $projectedKm - $lastKm;

            if ($deltaKm >= $limitKm) {
                return true;
            }
        }

        return false;
    }
}
