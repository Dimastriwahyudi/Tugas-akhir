<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganProduk extends Model
{
    protected $table    = 'kunjungan_produk';
    protected $fillable = [
        'kunjungan_id', 'produk_id', 'stok_masuk',
        'stok_keluar', 'harga_jual', 'harga_modal'
    ];

    public function produk()    { return $this->belongsTo(Produk::class); }
    public function kunjungan() { return $this->belongsTo(Kunjungan::class); }

    // Accessor
    public function getTerjualAttribute(): int
    {
        return $this->stok_masuk - $this->stok_keluar;
    }

    public function getProfitAttribute(): float
    {
        return $this->terjual * ($this->harga_jual - $this->harga_modal);
    }
}
