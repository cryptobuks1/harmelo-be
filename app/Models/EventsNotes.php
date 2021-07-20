<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class EventsNotes extends Model
{
    use HasFactory;
    protected $table = 'tbl_events_note';


    public static function add($user_id, $event_id, $notes) {

       $notes_list = json_decode($notes);   
       foreach($notes_list as $row) {
        $n = new EventsNotes();
        $n->event_id = $event_id;
        $n->user_id = $user_id;
        $n->type = $row->type;
        $n->notes = $row->notes;
        $n->save();
       }
    }
    public function booking() {
        return $this->belongsTo(Events::class, 'id');
    }
}
