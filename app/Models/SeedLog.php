<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeedLog extends Model
{
    protected $table = 'seed_log';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'tanggal_semai',
        'hari',
        'nama_benih',
        'varietas',
        'satuan',
        'jumlah',
        'sumber_benih',
        'data_sayur_id',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'tanggal_semai' => 'date',
        'jumlah' => 'decimal:2',
        'data_sayur_id' => 'integer',
        'user_id' => 'integer'
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relationship to DataSayur
    public function dataSayur()
    {
        return $this->belongsTo(DataSayur::class, 'data_sayur_id', 'id');
    }
}