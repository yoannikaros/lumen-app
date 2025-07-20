<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'action', 'table_name', 'record_id', 'details', 'ip_address', 'user_agent'
    ];

    public $timestamps = false;
    
    protected $dates = ['created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}