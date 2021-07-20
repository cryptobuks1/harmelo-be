<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class AutoLogin extends Model
{   
    use HasFactory;
    protected $table = 'tbl_auto_login';
    public static function insert($user_id,  $code, $redirect_to, $module) {
        $c = new AutoLogin();
        $c->user_id = $user_id;
        $c->code = $code;
        $c->redirect_to = $redirect_to;
        $c->module = $module;
        $c->save();
        $insert_id =  $c->id;
    }
}
