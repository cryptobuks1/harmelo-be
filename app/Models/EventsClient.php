<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsClient extends Model
{
    use HasFactory;

    protected $table = 'tbl_events_clients';

    public static function getClientsByEvent($event_id) {
        return EventsClient::where('event_id', $event_id)
            ->get();
    }
}
