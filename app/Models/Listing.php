<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    public static function getTeacherReview($user_id) {
        $rev = UserReview::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
        return $rev;
    }

    public static function getPendingApplicationList() {
        return Application::where('is_delete', '=', '0')
            ->where('status', '=', 'pending')
            ->orderBy('id', 'DESC')->get();
    }
}
