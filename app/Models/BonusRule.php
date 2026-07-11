<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'bonus_scheme_id',
        'min_menit',
        'max_menit',
        'nominal'
    ];

    public function scheme()
    {
        return $this->belongsTo(BonusScheme::class, 'bonus_scheme_id');
    }
}
