<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['nama', 'kategori', 'bonus_scheme_id'];

    public function details()
    {
        return $this->hasMany(ShiftDetail::class, 'shift_id');
    }

    public function bonusScheme()
    {
        return $this->belongsTo(BonusScheme::class, 'bonus_scheme_id');
    }

    public function usersWithDefault()
    {
        return $this->hasMany(User::class, 'default_shift_id');
    }
}
