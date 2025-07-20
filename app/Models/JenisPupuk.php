<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPupuk extends Model
{
    protected $table = 'jenis_pupuk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_pupuk', 'deskripsi', 'satuan', 'harga_per_satuan', 'status'
    ];

    protected $casts = [
        'harga_per_satuan' => 'decimal:2'
    ];

    public function pencatatanPupuk()
    {
        return $this->hasMany(PencatatanPupuk::class, 'jenis_pupuk_id');
    }
}