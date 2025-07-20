<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaKebun extends Model
{
    protected $table = 'area_kebun';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_area', 'deskripsi', 'luas_m2', 'kapasitas_tanaman', 'status'
    ];

    protected $casts = [
        'luas_m2' => 'decimal:2'
    ];

    public function nutrisiPupuk()
    {
        return $this->hasMany(NutrisiPupuk::class, 'area_id');
    }

    public function dataSayur()
    {
        return $this->hasMany(DataSayur::class, 'area_id');
    }
}