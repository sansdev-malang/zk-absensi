<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'nama_mesin',
        'nomor_mesin',
        'ip_address',
        'port',
        'comm_key',
        'firmware',
        'kapasitas',
        'status',
        'last_sync_at',
    ];

    protected $casts = [
        'status' => 'boolean',
        'last_sync_at' => 'datetime',
    ];
}
