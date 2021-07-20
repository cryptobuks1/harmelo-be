<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    use HasFactory;

    protected $table = 'tbl_post_reaction';

    public static function getReactionsByPostID($post_id) {
        return PostReaction::where('post_id', $post_id)
            ->where('is_delete', 0)
            ->get();
    }

    public static function getReactionsByPostIDS($post_id_array) {
        return PostReaction::whereIn('post_id', $post_id_array)
            ->where('is_delete', 0)
            ->get();
    }

}
