<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostShare extends Model
{
    use HasFactory;

    protected $table = 'tbl_post_share';

    //This will retrieve data by incremental id
    public static function getSharedPostSharesByID($id) {
        return PostShare::where('post_type', 'shared')
            ->where('post_id', $id)
            ->where('is_delete', 0)
            ->get();
    }

    //This will retrieve data by post_id
    public static function getSharesByPostID($post_id) {
        return PostShare::where('post_id', $post_id)
            ->where('is_delete', 0)
            ->get();
        /*return PostShare::where('post_type', 'original')
            ->where('post_id', $post_id)
            ->where('is_delete', 0)
            ->get(); */
    }

}
