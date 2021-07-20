<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $table = 'tbl_track';

    public static function getTrackDetailsByID($track_id) {
        return Track::where('id', $track_id)
            ->where('is_delete', 0)
            ->get();
    }
}
