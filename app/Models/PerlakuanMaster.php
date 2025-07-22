<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerlakuanMaster extends Model
{
    protected $table = 'perlakuan_master';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nama_perlakuan',
        'tipe',
        'deskripsi',
        'satuan_default'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    // Relationship to JadwalPerlakuan
    public function jadwalPerlakuan()
    {
        return $this->hasMany(JadwalPerlakuan::class, 'perlakuan_id');
    }
}