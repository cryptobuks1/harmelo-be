<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{
    use HasFactory;
    protected $table = 'tbl_coins';
    public static function insert($user_id, $coach_id, $source_id, $coins, $reasons, $type, $module) {
            $c  = new Coins();
            $c->use_id = $user_id;
            $c->coach_ud = $coach_id;
            $c->source_id = $source_id;
            $c->coins = $coins;
            $c->reasons = $reasons;
            $c->type = $type;
            $c->module = $module;
            $c->save();
            return  $c->id;
    }
}
