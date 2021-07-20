<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoints extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_points';
    public static function getCurrentPoints($user_id) {
        $points = 0;

        $result = UserPoints::where('user_id', $user_id)->get();
        if (count($result) > 0)
            return $points = (int) $result[0]->points;
        return $points;
    }
}
