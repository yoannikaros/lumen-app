<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutrisiPupukDetail extends Model
{
    protected $table = 'nutrisi_pupuk_detail';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nutrisi_pupuk_id', 'tandon_id', 'ppm', 'nutrisi_ditambah_ml',
        'air_ditambah_liter', 'ph', 'suhu_air', 'keterangan'
    ];

    protected $casts = [
        'ppm' => 'decimal:2',
        'nutrisi_ditambah_ml' => 'decimal:2',
        'air_ditambah_liter' => 'decimal:2',
        'ph' => 'decimal:2',
        'suhu_air' => 'decimal:2'
    ];

    public function nutrisiPupuk()
    {
        return $this->belongsTo(NutrisiPupuk::class, 'nutrisi_pupuk_id');
    }

    public function tandon()
    {
        return $this->belongsTo(Tandon::class, 'tandon_id');
    }
}