<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSayur extends Model
{
    protected $table = 'data_sayur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_tanam', 'jenis_sayur', 'varietas', 'area_id', 'jumlah_bibit',
        'metode_tanam', 'jenis_media', 'tanggal_panen_target', 'tanggal_panen_aktual',
        'status_panen', 'jumlah_panen_kg', 'penyebab_gagal', 'keterangan', 'user_id'
    ];

    protected $casts = [
        'tanggal_tanam' => 'date',
        'tanggal_panen_target' => 'date',
        'tanggal_panen_aktual' => 'date',
        'jumlah_panen_kg' => 'decimal:2'
    ];

    public function area()
    {
        return $this->belongsTo(AreaKebun::class, 'area_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}