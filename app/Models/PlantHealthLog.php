<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantHealthLog extends Model
{
    protected $table = 'plant_health_log';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'data_sayur_id',
        'gejala',
        'jumlah_tanaman_terdampak',
        'tindakan',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'data_sayur_id' => 'integer',
        'jumlah_tanaman_terdampak' => 'integer',
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