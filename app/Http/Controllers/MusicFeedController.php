<?php

namespace App\Http\Controllers;

use App\Models\MusicFeed;
use App\Models\PostReaction;
use App\Models\PostShare;
use App\Models\Track;
use App\Models\User;
use App\Models\UserFollower;
use App\Models\UserFollowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MusicFeedController extends Controller
{

    public function musicfeeduploadcover_v2(Request $request) {

        $userID = $request->user_id;
        $postID = $request->post_id;

        $albumArt_image_fileArray = $request->albumArt_image_fileArray !== 'none' ?
            json_decode($request->albumArt_image_fileArray) : null;

        if ($albumArt_image_fileArray != null) {
            $file_name = $albumArt_image_fileArray[0]->file_name;
            Storage::disk('s3')->put('public/musicfeed/' . $userID . '/' . 'avatar/' . $file_name, base64_decode($albumArt_image_fileArray[0]->file), 'public');

            $columnToUpdate = [
                'image_path' => 'public/musicfeed/' . $userID . '/' . 'avatar/' . $file_name,
                'updated_at' => Carbon::now()
            ];
            $trackID = MusicFeed::where('post_id', $postID)
                ->get();

            $track = Track::where('id', $trackID->track_id)
                        ->update($columnToUpdate);

            if ($track) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => 'public/musicfeed/' . $userID . '/' . 'avatar/' . $file_name
                ], 200);
            }
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ], 200);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function musicfeedgetpostbyuserid(Request $request) {

        $currentUserID = $request->user_id;

        $music_feed = MusicFeed::where('is_delete', 0)
            ->where('user_id', $currentUserID)
            ->where('is_shared', 0)
            ->limit(15)
            ->orderBy('id', 'DESC')
            ->get();

        $arrays = [];

        foreach ($music_feed as $feed) {
            if ($feed->is_album == 1) {
                // add logic for album post later
            } else {
                //$postOwnerID = $feed->source_user_id == null ? $feed->user_id : $feed->source_user_id;
                $feed['track_details'] = Track::getTrackDetailsByID($feed->track_id);
                $feed['user_details'] = User::getUserDetailsByID($feed->user_id);
                $feed['original_poster_details'] = User::getUserDetailsByID($feed->source_user_id);
                $feed['original_post_details'] = MusicFeed::getPostByID($feed->source_post_id);
                $feed['reactions'] = PostReaction::getReactionsByPostID($feed->id);
                $feed['shares'] = PostShare::getSharesByPostID($feed->id);
                $arrays[] = $feed;
            }
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $arrays//array_merge($arrays, MusicFeed::getSharedPost($currentUserID))
        ]);
    }

    public function musicfeedgetolderpost(Request $request) {
        $lastRenderedID = $request->last_rendered_id;

        $currentUserID = $request->user_id;


        $music_feed = MusicFeed::where('is_delete', 0)
            ->where('id', '<', $lastRenderedID)
            ->whereIn('user_id', array_merge($this->getFollowersAndFollowings($currentUserID), [$currentUserID]))
            ->limit(5)
            ->orderBy('id', 'DESC')
            ->get();

        $arrays = [];

        foreach ($music_feed as $feed) {
            if ($feed->is_album == 1) {
                // add logic for album post later
            } else {
                //$postOwnerID = $feed->source_user_id == null ? $feed->user_id : $feed->source_user_id;
                $feed['track_details'] = Track::getTrackDetailsByID($feed->track_id);
                $feed['user_details'] = User::getUserDetailsByID($feed->user_id);
                $feed['original_poster_details'] = User::getUserDetailsByID($feed->source_user_id);
                $feed['original_post_details'] = MusicFeed::getPostByID($feed->source_post_id);
                $feed['reactions'] = PostReaction::getReactionsByPostID($feed->id);
                $feed['shares'] = PostShare::getSharesByPostID($feed->id);

                $arrays[] = $feed;
            }
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $arrays[0]//array_merge($arrays, MusicFeed::getSharedPost($currentUserID))
        ]);
    }

    public function getFollowersAndFollowings($currentUserID) {
        //Pluck the IDs of the current user's followers
        $followers = UserFollower::where('status', 'accepted')
            ->where('target_user_id', $currentUserID)
            ->pluck('follower_id')->toArray();

        //Pluck the IDs of the current user's followings
        $followings = UserFollower::where('status', 'accepted')
            ->where('follower_id', $currentUserID)
            ->pluck('target_user_id')->toArray();

        return array_merge($followers, $followings);
    }

    public function getFollowers($currentUserID) {
        //Pluck the IDs of the current user's followers
        $followers = UserFollower::where('status', 'accepted')
            ->where('target_user_id', $currentUserID)
            ->pluck('follower_id')->toArray();

        return $followers;
    }

    public function getFollowings($currentUserID) {
        //Pluck the IDs of the current user's followings
        $followings = UserFollower::where('status', 'accepted')
            ->where('follower_id', $currentUserID)
            ->pluck('target_user_id')->toArray();

        return $followings;
    }

    public function musicfeedgetrandomstrangerusers(Request $request) {
        /**
         * Get the IDs of the current followings/followers and then exclude
         */
        $currentUserID = $request->user_id;

        //Pluck the IDs of the current user's followers
        $followers = UserFollower::where('target_user_id', $currentUserID)
            ->where('status', 'accepted')
            ->where('status', 'pending')
            ->pluck('follower_id')->toArray();

        //Pluck the IDs of the current user's followings
        $followings = UserFollower::where('follower_id', $currentUserID)
            ->where('status', 'accepted')
            ->orWhere('status', 'pending')
            ->pluck('target_user_id')->toArray();

        //dd(array_merge($followers, $followings));
        //exclude followers,followings,and self from the query
        $random_users = User::whereNotIn('id', array_merge($followings, [$currentUserID]))
            ->inRandomOrder()
            ->limit(5)
            ->get();

        //dd($random_users->toArray());

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $random_users->toArray()
        ]);
    }

    public function musicfeedsharepost(Request $request) {

        $music_feed = new MusicFeed();
        $music_feed->caption = $request->description;
        $music_feed->is_album = $request->is_album;
        $music_feed->track_id = $request->track_id;
        $music_feed->user_id = $request->shared_by;
        $music_feed->privacy = $request->privacy;
        $music_feed->is_shared = 1;
        $music_feed->source_user_id = $request->source_user_id; //always refer the original poster
        $music_feed->source_post_id = $request->source_post_id; //always share the original post
        $music_feed->is_delete = 0;
        $music_feed->created_at = Carbon::now();
        $music_feed->updated_at = Carbon::now();
        $saved = $music_feed->save();

        if ($saved) {
            $post_share = new PostShare();
            $post_share->post_id = $request->post_id; //use the post id of the sharer to credits the share count to him
            $post_share->shared_by = $request->shared_by;
            $post_share->description = $request->description;
            $post_share->privacy = $request->privacy;
            $post_share->is_delete = 0;
            $post_share->created_at = Carbon::now();
            $post_share->updated_at = Carbon::now();

            $save_share = $post_share->save();

            if ($save_share) {
                $arrays = [];

                if ($music_feed->is_album == 1) {
                    // add logic for album post later
                } else {
                    //$postOwnerID = $feed->source_user_id == null ? $feed->user_id : $feed->source_user_id;
                    $music_feed['track_details'] = Track::getTrackDetailsByID($music_feed->track_id);
                    $music_feed['user_details'] = User::getUserDetailsByID($music_feed->user_id);
                    $music_feed['original_poster_details'] = User::getUserDetailsByID($music_feed->source_user_id);
                    $music_feed['original_post_details'] = MusicFeed::getPostByID($music_feed->source_post_id);
                    $music_feed['reactions'] = PostReaction::getReactionsByPostID($music_feed->id);
                    $music_feed['shares'] = PostShare::getSharesByPostID($music_feed->id);
                    $arrays[] = $music_feed;
                }

                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $arrays
                ],200);
            } else {
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ],200);
            }
        } else {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ],200);
        }
    }

    public function musicfeedupdatereacttopost(Request $request) {

        $reactionId = $request->reaction_id;
        $newReaction = $request->new_reaction;
        $columToUpdate = ['reaction_type' => $newReaction, 'updated_at' => Carbon::now()];

        if ($newReaction === 'unreact')
            $columToUpdate = ['is_delete' => 1, 'updated_at' => Carbon::now()];


        $update_reaction = PostReaction::where('id', $reactionId)
            ->update($columToUpdate);

        if ($update_reaction)
            return response()->json(['status'=>1,'msg'=>'ok'], 200);


        return response()->json(['status'=>0,'msg'=>'bad'], 400);
    }

    public function musicfeedreacttopost(Request $request) {

        $post_reaction = new PostReaction();
        $post_reaction->post_id = $request->post_id;
        $post_reaction->reacted_by = $request->reacted_by;
        $post_reaction->reaction_type = $request->reaction_type;
        $post_reaction->is_delete = 0;
        $post_reaction->created_at = Carbon::now();
        $post_reaction->updated_at = Carbon::now();

        $save_reaction = $post_reaction->save();

        if (!$save_reaction) {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ],200);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $post_reaction->toArray()
        ],200);
    }


    public function musicfeedgetpost(Request $request) {

        $currentUserID = $request->user_id;

        $music_feed = MusicFeed::where('is_delete', 0)
            ->whereIn('user_id', array_merge($this->getFollowings($currentUserID), [$currentUserID]))
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get();

        $arrays = [];

        foreach ($music_feed as $feed) {
            if ($feed->is_album == 1) {
                // add logic for album post later
            } else {
                //$postOwnerID = $feed->source_user_id == null ? $feed->user_id : $feed->source_user_id;
                $feed['track_details'] = Track::getTrackDetailsByID($feed->track_id);
                $feed['user_details'] = User::getUserDetailsByID($feed->user_id);
                $feed['original_poster_details'] = User::getUserDetailsByID($feed->source_user_id);
                $feed['original_post_details'] = MusicFeed::getPostByID($feed->source_post_id);
                $feed['reactions'] = PostReaction::getReactionsByPostID($feed->id);
                $feed['shares'] = PostShare::getSharesByPostID($feed->id);
                $arrays[] = $feed;
            }
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $arrays//array_merge($arrays, MusicFeed::getSharedPost($currentUserID))
        ]);
    }

    public function musicfeedinsertpost(Request $request) {

        //Get track and track image objects here
        $image_fileArray = $request->image_fileArray !== 'none' ? json_decode($request->image_fileArray) : null; //For the thumbnail
        $track_fileArray = $request->track_fileArray !== 'none' ? json_decode($request->track_fileArray) : null; //For the music

        if ($track_fileArray != null) {
            $track = new Track();
            $track->user_id = $request->user_id;
            $track->title = $request->title;
            $track->track_path = $request->track_path . $track_fileArray[0]->file_ext;
            $track->image_path = $image_fileArray != null ? $request->image_path . $image_fileArray[0]->file_ext : null;
            $track->privacy = $request->privacy;
            $track->producer = $request->producer;
            $track->is_delete = 0;
            $track->created_at = Carbon::now();
            $track->updated_at = Carbon::now();
            $track_saved = $track->save();

            if ($track_saved) {
                // Save track image here if one is provided, else dont
                if ($image_fileArray != null) Storage::disk('s3')->put($request->image_path . $image_fileArray[0]->file_ext, base64_decode($image_fileArray[0]->file), 'public');

                $music_feed = new MusicFeed();
                $music_feed->caption = $request->caption;
                $music_feed->is_album = $request->is_album;
                $music_feed->track_id = $track->id;
                $music_feed->user_id = $request->user_id;
                $music_feed->privacy = $request->privacy;
                $music_feed->source_user_id = $request->user_id;
                $music_feed->is_delete = 0;
                $music_feed->created_at = Carbon::now();
                $music_feed->updated_at = Carbon::now();

                if ($music_feed->save()) {
                    MusicFeed::where('id', $music_feed->id)
                        ->update(['source_post_id' =>  $music_feed->id]);
                    // Save track here
                    //Storage::disk('s3')->put('public/musicfeed/'.$request->user_slug.'/track'.'/'.$track_fileArray[0]->file_name, base64_decode($track_fileArray[0]->file), 'public');
                    Storage::disk('s3')->put($request->track_path . $track_fileArray[0]->file_ext, base64_decode($track_fileArray[0]->file), 'public');

                    return response()->json([
                        'msg' => 'ok',
                        'status' => 1,
                        'data' => []
                    ], 200);
                } else {
                    return response()->json([
                        'msg' => 'bad',
                        'status' => 0,
                        'data' => []
                    ], 200);
                }
            } else {
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ], 200);
            }
        } else {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ], 200);
        }
        //End of Function
    }

}
