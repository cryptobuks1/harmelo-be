<?php

namespace App\Http\Controllers;

use App\Models\Instrument;
use Illuminate\Http\Request;

class InstrumentController extends Controller
{
    protected $table = 'tbl_instrument';

    public static function getInstrumentByID($instrument_id) {
        return Instrument::where('id', $instrument_id)->first();
    }
}
