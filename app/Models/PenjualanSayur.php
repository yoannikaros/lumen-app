<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanSayur extends Model
{
    protected $table = 'penjualan_sayur';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_penjualan', 'nama_pembeli', 'tipe_pembeli', 'alamat_pembeli',
        'jenis_sayur', 'jumlah_kg', 'harga_per_kg', 'total_harga',
        'metode_pembayaran', 'status_pembayaran', 'keterangan', 'user_id'
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'jumlah_kg' => 'decimal:2',
        'harga_per_kg' => 'decimal:2',
        'total_harga' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}