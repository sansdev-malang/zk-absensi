<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftDetail extends Model
{
    protected $fillable = ['shift_id', 'hari', 'jam_masuk', 'jam_pulang', 'is_cross_day'];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
