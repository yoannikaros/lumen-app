<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanPupuk extends Model
{
    protected $table = 'pencatatan_pupuk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal', 'jenis_pupuk_id', 'jumlah_pupuk', 'satuan', 'keterangan', 'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_pupuk' => 'decimal:2'
    ];

    public function jenisPupuk()
    {
        return $this->belongsTo(JenisPupuk::class, 'jenis_pupuk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}