<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal',
        'shift_detail_id',
        'jam_masuk',
        'jam_pulang',
        'menit_terlambat',
        'menit_pulang_cepat',
        'status_kehadiran',
        'bonus_didapat'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shiftDetail()
    {
        return $this->belongsTo(ShiftDetail::class);
    }
}
