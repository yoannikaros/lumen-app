<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanPupuk extends Model
{
    protected $table = 'pencatatan_pupuk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal', 'area_id', 'tandon_id', 'jenis_pupuk_id', 'jumlah_pupuk', 
        'satuan', 'bentuk', 'volume_liter', 'keterangan', 'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_pupuk' => 'decimal:2',
        'volume_liter' => 'decimal:2'
    ];

    public function jenisPupuk()
    {
        return $this->belongsTo(JenisPupuk::class, 'jenis_pupuk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(AreaKebun::class, 'area_id');
    }

    public function tandon()
    {
        return $this->belongsTo(Tandon::class, 'tandon_id');
    }
}