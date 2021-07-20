<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory; 
    protected $table = 'tbl_user_reviewer';
    protected $appends = ['reviewer'];

    public function getReviewerAttribute() {
        $user = User::where('id', '=', $this->reviewer_id)
        ->first();

        return $user;
    }

    public static function getUserRating($user_id) {
        $rev_list = UserReview::where('user_id', $user_id)->get();

        $recom_count= 0;
        $rev_len = $rev_list->count();
        foreach($rev_list as $rev) {
            if ($rev->is_recommended == 1) {
                $recom_count++;
            }
        }
        return $rev_len == 0 ? 0 : (($recom_count/$rev_len)*5);
    }
}
