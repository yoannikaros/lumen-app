<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tandon extends Model
{
    protected $table = 'tandon';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area_id', 'kode_tandon', 'nama_tandon', 'kapasitas_liter', 
        'status', 'keterangan'
    ];

    protected $casts = [
        'kapasitas_liter' => 'decimal:2'
    ];

    public function area()
    {
        return $this->belongsTo(AreaKebun::class, 'area_id');
    }

    public function nutrisiPupukDetail()
    {
        return $this->hasMany(NutrisiPupukDetail::class, 'tandon_id');
    }

    public function pencatatanPupuk()
    {
        return $this->hasMany(PencatatanPupuk::class, 'tandon_id');
    }
}