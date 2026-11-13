<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
    ];

    public function motors(): HasMany
    {
        return $this->hasMany(Motor::class, 'id_pelanggan');
    }

    public function motor(): HasMany
    {
        return $this->hasMany(Motor::class, 'id_pelanggan');
    }

    public function transaksiServis(): HasMany
    {
        return $this->hasMany(TransaksiServis::class, 'id_pelanggan');
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiServis::class, 'id_pelanggan');
    }

    public function getLastServisAttribute()
    {
        return $this->transaksiServis()->latest('tanggal_servis')->first();
    }
}
