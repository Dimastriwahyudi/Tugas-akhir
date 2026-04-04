<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Warung extends Model
{
    use LogsActivity;

    protected $table    = 'warung';
    protected $fillable = [
        'nama_pemilik', 'nama_warung', 'latitude', 'longitude',
        'alamat', 'foto', 'status', 'catatan', 'sales_id'
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function kunjunganTerakhir()
    {
        return $this->hasOne(Kunjungan::class)->latestOfMany();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_pemilik', 'status'])
            ->logOnlyDirty();
    }
}
