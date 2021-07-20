<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogs extends Model
{
    use HasFactory;
    protected $table = 'tbl_activity_logs';
    public static function insert($user_id, $client_id,  $source_id, $name, $title, $icon, $module, $action, $desc) {
        $e = new ActivityLogs();
        $e->user_id = $user_id;    
        $e->client_id = $client_id;
        $e->source_id = $source_id;
        $e->name = $name;
        $e->title = $title;
        $e->icon = $icon;
        $e->module = $module;
        $e->action = $action;
        $e->descriptions = $desc;
        $e->save();       
    }
}
