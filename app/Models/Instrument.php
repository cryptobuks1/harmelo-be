<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    use HasFactory;

    protected $table = 'tbl_instrument';

    

    public static function getInstrumentByID($instrument_id) {
        $arrays = [];
        foreach (explode(',', $instrument_id) as $i) {
            $instrument = Instrument::where('id', $i)->first();
            if ($instrument) {
                $arrays[] = $instrument;
            }
        }
        return $arrays;
    }
}
