<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCV extends Model
{
    use HasFactory;

    protected $table = 'tbl_user_cv';

    public static function getCVByUserID($user_id) {
        return UserCV::where('user_id', $user_id)
            ->where('is_delete', 0)
            ->get();
    }
}
