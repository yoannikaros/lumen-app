<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanDetailBatch extends Model
{
    protected $table = 'penjualan_detail_batch';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'penjualan_id',
        'data_sayur_id',
        'qty_kg',
        'keterangan'
    ];

    protected $casts = [
        'id' => 'integer',
        'penjualan_id' => 'integer',
        'data_sayur_id' => 'integer',
        'qty_kg' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship to PenjualanSayur
     */
    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(PenjualanSayur::class, 'penjualan_id', 'id');
    }

    /**
     * Relationship to DataSayur
     */
    public function dataSayur(): BelongsTo
    {
        return $this->belongsTo(DataSayur::class, 'data_sayur_id', 'id');
    }
}