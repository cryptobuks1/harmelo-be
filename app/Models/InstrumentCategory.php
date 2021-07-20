<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentCategory extends Model
{
    use HasFactory;

    protected $table = 'tbl_instrument_category';

    public function instruments() {
        return $this->hasMany(Instrument::class, 'category_id');
    }

}
