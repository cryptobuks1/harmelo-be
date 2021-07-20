<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachInstrumentRate extends Model
{
    use HasFactory;

    protected $table = 'tbl_coach_instrument_rate';

    public function instrument() {
        return $this->belongsTo(Instrument::class, 'instrument_id');
    }

    public function instrumentrate() {
        return $this->hasMany(CoachInstrumentRate::class, 'user_id');
    }
}
