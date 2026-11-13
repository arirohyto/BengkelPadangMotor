<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'spareparts';

    protected $fillable = [
        'kode_part',
        'nama_barang',
        'kategori_komponen',
        'stok',
        'harga_beli',
        'harga_jual',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    public function transaksiServis(): BelongsToMany
    {
        return $this->belongsToMany(TransaksiServis::class, 'detail_sparepart_transaksi', 'id_sparepart', 'id_transaksi')
            ->withPivot('jumlah', 'harga_satuan', 'subtotal')
            ->withTimestamps();
    }

    public function getHargaJualFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    public function getHargaBeliFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_beli, 0, ',', '.');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stok < 5;
    }

    public function getMarginAttribute(): float
    {
        return (float)($this->harga_jual - $this->harga_beli);
    }

    public function getPresentaseMarginAttribute(): float
    {
        return $this->harga_beli > 0 ? (float)(($this->harga_jual - $this->harga_beli) / $this->harga_beli * 100) : 0.0;
    }

    public function getPersentaseMarginAttribute(): float
    {
        return $this->getPresentaseMarginAttribute();
    }
}
