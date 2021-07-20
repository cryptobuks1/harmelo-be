<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    //protected $appends = ['userdetails'];

    protected $table = 'tbl_application';

    /*public function getUserdetailsAttribute() {
        $user = User::where('id', '=', $this->user_id)
        ->get();

        return $user[0];
    }*/
}
