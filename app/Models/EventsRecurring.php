<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsRecurring extends Model
{
    use HasFactory;

    protected $table = 'tbl_events_recurring';

    public static function getRecurringStatusByEvent($event_id) {
        return EventsRecurring::where('event_id', $event_id)
            ->get();
    }
}
