<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianBenihDetail extends Model
{
    protected $table = 'pembelian_benih_detail';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'belanja_modal_id',
        'nama_benih',
        'varietas',
        'qty',
        'unit',
        'harga_per_unit',
        'keterangan'
    ];

    protected $casts = [
        'id' => 'integer',
        'belanja_modal_id' => 'integer',
        'qty' => 'decimal:2',
        'harga_per_unit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship to BelanjaModal
     */
    public function belanjaModal(): BelongsTo
    {
        return $this->belongsTo(BelanjaModal::class, 'belanja_modal_id', 'id');
    }
}