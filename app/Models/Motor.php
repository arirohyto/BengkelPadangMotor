<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Motor extends Model
{
    use HasFactory;

    protected $table = 'motor';

    protected $fillable = [
        'id_pelanggan',
        'no_polisi',
        'merk',
        'tipe_motor',
        'tahun_pembuatan',
    ];

    public const TIPE_MOTOR = ['Matic', 'Bebek', 'Sport'];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function transaksiServis(): HasMany
    {
        return $this->hasMany(TransaksiServis::class, 'id_motor');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiServis::class, 'id_motor');
    }

    public function transaksiTerakhir()
    {
        return $this->hasOne(TransaksiServis::class, 'id_motor')->latestOfMany('tanggal_servis');
    }

    public function getUsiaMotorAttribute(): int
    {
        return Carbon::now()->year - $this->tahun_pembuatan;
    }

    public function getLastServisAttribute()
    {
        return $this->transaksiServis()->latest('tanggal_servis')->first();
    }

    public function getFullDescriptionAttribute(): string
    {
        return "{$this->merk} ({$this->tipe_motor}) - {$this->no_polisi}";
    }
}
