<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFollower;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserFollowerController extends Controller
{
    public function userfollowergetuserfollowingcountbyslug(Request $request) {
        $userID = User::getUserIDBySlug($request->slug);

        $followings = UserFollower::where('status', 'accepted')
            ->where('follower_id', $userID)
            ->pluck('target_user_id')->toArray();

        $user = User::whereIn('id', $followings)->orderBy('name')->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $user
        ], 200);
    }

    public function userfollowergetuserfollowercountbyslug(Request $request) {
        $userID = User::getUserIDBySlug($request->slug);

        $followers = UserFollower::where('status', 'accepted')
            ->where('target_user_id', $userID)
            ->pluck('follower_id')->toArray();

        $user = User::whereIn('id', $followers)->orderBy('name')->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $user
        ], 200);
    }

    public function userfollowergetfriendrequestlist(Request $request) {
        $userID = $request->user_id;

        $whereClause = ['target_user_id'=>$userID, 'status'=>'pending', 'is_delete'=>0 ];
        $friend_request_list = UserFollower::where($whereClause)
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get();

        $request_list_array = [];

        if ($friend_request_list->count() > 0) {
            foreach ($friend_request_list as $request) {
                $request['user_details'] = User::getUserDetailsByID($request->follower_id);
                $request_list_array[] = $request;
            }
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $request_list_array
        ], 200);
    }

    public function userfollowergeneratenewsuggesteruser(Request $request) {
        $currentUserID = $request->user_id;
        $currentlyShowedIDs = json_decode($request->current_ids);

        //Pluck the IDs of the current user's followers
        $followers = UserFollower::where('status', 'accepted')
            ->where('status', 'pending')
            ->where('target_user_id', $currentUserID)
            ->pluck('follower_id')->toArray();

        //Pluck the IDs of the current user's followings
        $followings = UserFollower::where('follower_id', $currentUserID)
            ->where('status', 'accepted')
            ->orWhere('status', 'pending')
            ->pluck('target_user_id')->toArray();

        //exclude followers,followings,and self from the query
        $random_user = User::whereNotIn('id', array_merge($followings, $currentlyShowedIDs, [$currentUserID]))
            ->inRandomOrder()
            ->limit(1)
            ->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $random_user->toArray()
        ]);
    }

    public function userfollowerfollowuser(Request $request) {
        $follow = new UserFollower();
        $follow->target_user_id = $request->target_user_id;
        $follow->follower_id = $request->follower_id;
        $follow->is_delete = 0;
        $follow->created_at = Carbon::now();
        $follow->updated_at = Carbon::now();

        $follow_save = $follow->save();
        if ($follow_save) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $follow->toArray()
            ], 200);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function userfollowerunfollowuser(Request $request) {
        $unfollow = UserFollower::where('target_user_id', $request->target_user_id)
            ->where('follower_id', $request->follower_id)
            ->update(['status' => 'unfollowed', 'updated_at' => Carbon::now()]);

        if ($unfollow) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ], 200);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function userfollowerdeclinerequest(Request $request) {
        $decline = UserFollower::where('target_user_id', $request->target_user_id)
            ->where('follower_id', $request->follower_id)
            ->where('status', 'pending')
            ->update(['status' => 'declined', 'updated_at' => Carbon::now()]);

        if ($decline) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ], 200);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function userfollowercancelrequest(Request $request) {
        $cancel = UserFollower::where('target_user_id', $request->target_user_id)
            ->where('follower_id', $request->follower_id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'updated_at' => Carbon::now()]);

        if ($cancel) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ], 200);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function userfolloweracceptrequest(Request $request) {
        $accept = UserFollower::where('target_user_id', $request->target_user_id)
            ->where('follower_id', $request->follower_id)
            ->where('status', 'pending')
            ->update(['status' => 'accepted', 'updated_at' => Carbon::now()]);

        if ($accept) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ], 200);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);
    }
}
