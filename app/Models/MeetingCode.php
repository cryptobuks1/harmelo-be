<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class MeetingCode extends Model
{   
    use HasFactory;
    protected $table = 'tbl_meeting_code';
    public static function insert($user_id,  $source_id,  $code, $url, $module) {
        $c = new MeetingCode();
        $c->user_id = $user_id;
        $c->source_id = $source_id;
        $c->code = $code;
        $c->url = $url;
        $c->module = $module;
        $c->save();
        $insert_id =  $c->id;
    }
}
