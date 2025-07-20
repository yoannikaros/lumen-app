<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BelanjaModal extends Model
{
    protected $table = 'belanja_modal';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_belanja', 'kategori', 'deskripsi', 'jumlah', 'satuan',
        'nama_toko', 'alamat_toko', 'metode_pembayaran', 'bukti_pembayaran',
        'keterangan', 'user_id'
    ];

    protected $casts = [
        'tanggal_belanja' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}