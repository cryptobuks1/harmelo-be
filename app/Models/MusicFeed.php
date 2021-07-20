<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MusicFeed extends Model
{
    use HasFactory;

    protected $table = 'tbl_music_feed';

    public static function getPostByID($post_id) {
        return MusicFeed::where('is_delete', 0)
            ->where('id', $post_id)
            ->get();
    }

    public static function getSharedPost($currentUserID) {
        $followers = UserFollower::where('status', 'accepted')
            ->where('status', 'pending')
            ->where('target_user_id', $currentUserID)
            ->pluck('follower_id')->toArray();

        //Pluck the IDs of the current user's followings
        $followings = UserFollower::where('status', 'accepted')
            ->where('status', 'pending')
            ->where('follower_id', $currentUserID)
            ->pluck('target_user_id')->toArray();

        $commonFriendsID = array_merge($followers, $followings, [$currentUserID]);

        //Get post_id of Shared Posts
        $shared_post = PostShare::where('is_delete', 0)
            ->whereIn('shared_by', $commonFriendsID)
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get();

        $shared_post_array = [];

        foreach ($shared_post as $shared) {
            $shared_post_array[] = MusicFeed::getPostByID($shared->post_id)[0];
            $shares_post_array['shares'] = PostShare::getSharedPostSharesByID($shared->id);
        }

        return $shared_post_array;
        //Get Music Feed Posts
    }
}
