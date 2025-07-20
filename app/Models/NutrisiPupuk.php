<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutrisiPupuk extends Model
{
    protected $table = 'nutrisi_pupuk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_pencatatan', 'area_id', 'jumlah_tanda_air', 'jumlah_pupuk_ml',
        'jumlah_air_liter', 'ppm_sebelum', 'ppm_sesudah', 'ph_sebelum',
        'ph_sesudah', 'suhu_air', 'kondisi_cuaca', 'keterangan', 'user_id'
    ];

    protected $casts = [
        'tanggal_pencatatan' => 'date',
        'jumlah_tanda_air' => 'decimal:2',
        'jumlah_pupuk_ml' => 'decimal:2',
        'jumlah_air_liter' => 'decimal:2',
        'ppm_sebelum' => 'decimal:2',
        'ppm_sesudah' => 'decimal:2',
        'ph_sebelum' => 'decimal:2',
        'ph_sesudah' => 'decimal:2',
        'suhu_air' => 'decimal:2'
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