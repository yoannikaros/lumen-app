<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPerlakuan extends Model
{
    protected $table = 'jadwal_perlakuan';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'minggu_ke',
        'hari_dalam_minggu',
        'area_id',
        'tandon_id',
        'perlakuan_id',
        'dosis',
        'satuan',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'tanggal' => 'date',
        'minggu_ke' => 'integer',
        'area_id' => 'integer',
        'tandon_id' => 'integer',
        'perlakuan_id' => 'integer',
        'dosis' => 'decimal:2',
        'user_id' => 'integer'
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to AreaKebun
    public function area()
    {
        return $this->belongsTo(AreaKebun::class, 'area_id');
    }

    // Relationship to Tandon
    public function tandon()
    {
        return $this->belongsTo(Tandon::class, 'tandon_id');
    }

    // Relationship to PerlakuanMaster
    public function perlakuan()
    {
        return $this->belongsTo(PerlakuanMaster::class, 'perlakuan_id');
    }
}