<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Produk extends Model
{

    protected $table    = 'produk';    
    protected $fillable = ['nama', 'harga_jual', 'harga_modal', 'satuan', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function kunjunganProduk()
    {
        return $this->hasMany(KunjunganProduk::class);
    }
}
