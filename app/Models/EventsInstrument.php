<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsInstrument extends Model
{
    use HasFactory;

    protected $table = 'tbl_events_instrument';

    public static function insert($event_id, $parent_id, $user_id, $instrument_name) {
        $i = new EventsInstrument();
        $i->event_id = $event_id;
        $i->parent_event_id = $parent_id;
        $i->user_id = $user_id;
        $i->instrument_name = $instrument_name;
        $i->save();
    }
}
