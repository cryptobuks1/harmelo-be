<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Contacts extends Model
{   
    use HasFactory;
    protected $table = 'tbl_contacts';
    public static function add($user_id,  $client_id, $email, $phone, $name, $avatar) {
        $c = new Contacts();
        $c->user_id = $user_id;
        $c->client_id = $client_id;
        $c->email = $email;
        $c->phone = $phone;
        $c->name = $name;
        $c->avatar = $avatar;
        $c->save();
        return $c->id;
    }

}
