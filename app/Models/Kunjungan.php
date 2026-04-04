<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table    = 'kunjungan';
    protected $fillable = [
        'warung_id', 'sales_id', 'tanggal_kunjungan',
        'total_harga_jual', 'total_modal', 'profit', 'catatan'
    ];

    protected $casts = ['tanggal_kunjungan' => 'datetime'];

    public function warung()   { return $this->belongsTo(Warung::class); }
    public function sales()    { return $this->belongsTo(User::class, 'sales_id'); }
    public function produk()   { return $this->hasMany(KunjunganProduk::class); }
}
