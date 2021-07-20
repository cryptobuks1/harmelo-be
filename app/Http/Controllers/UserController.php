<?php

namespace App\Http\Controllers;

use App\Models\MusicFeed;
use App\Models\PostReaction;
use App\Models\User;
use App\Models\UserFollower;
use App\Models\UserProfileVisitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery\Undefined;

class UserController extends Controller
{

    public function changeapptheme(Request $request) {
        $currentUserID = $request->user_id;
        $newTheme = $request->theme;

        $update = User::where('id', $currentUserID)
            ->update(['app_theme'=>$newTheme]);
        
        if ($update) {
            return response()->json([
                'msg'=>'ok',
                'status'=>1
            ]);
        }
        return response()->json([
            'msg'=>'ok',
            'status'=>0
        ]);
    }

    public function usergetreactionscount(Request $request) {
        $currentUserID = $request->user_id;

        $music_feed = MusicFeed::where('user_id', $currentUserID)->pluck('id');
        $post_react = PostReaction::whereIn('post_id', $music_feed->toArray())->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $post_react
        ]);
    }

    public function useruploadavatar(Request $request) {

        $userID = $request->user_id;
        $milliseconds = round(microtime(true) * 1000);

        $file_name = '';

        $avatar_fileArray = $request->avatar_fileArray !== 'none' ? json_decode($request->avatar_fileArray) : null; //For the avatar

        if ($avatar_fileArray != null) {
            $file_name = $userID.''.$milliseconds.$avatar_fileArray[0]->file_ext;
            Storage::disk('s3')->put('public/user/' . $userID . '/' . 'avatar/' . $file_name, base64_decode($avatar_fileArray[0]->file), 'public');

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $file_name
            ], 200);
        }
    }

    public function useruploadcover(Request $request) {

        $userID = $request->user_id;
        $milliseconds = round(microtime(true) * 1000);

        $file_name = '';

        $cover_fileArray = $request->cover_fileArray !== 'none' ? json_decode($request->cover_fileArray) : null; //For the avatar

        if ($cover_fileArray != null) {
            $file_name = $userID.''.$milliseconds.$cover_fileArray[0]->file_ext;
            Storage::disk('s3')->put('public/user/' . $userID . '/' . 'cover/' . $file_name, base64_decode($cover_fileArray[0]->file), 'public');

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $file_name
            ], 200);
        }
    }

    public function useruploadavatar_v2(Request $request) {

        $userID = $request->user_id;

        $avatar_fileArray = $request->avatar_fileArray !== 'none' ? json_decode($request->avatar_fileArray) : null; //For the avatar

        if ($avatar_fileArray != null) {
            $file_name = $avatar_fileArray[0]->file_name;
            Storage::disk('s3')->put('public/user/' . $userID . '/' . 'avatar/' . $file_name, base64_decode($avatar_fileArray[0]->file), 'public');

            $columnToUpdate = [
                'avatar' => $file_name,
                'updated_at' => Carbon::now()
            ];
            $profile = User::where('id', $userID)
            ->update($columnToUpdate);

            if ($profile) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $file_name
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

    public function useruploadcover_v2(Request $request) {
        $userID = $request->user_id;

        $cover_fileArray = $request->cover_fileArray !== 'none' ? json_decode($request->cover_fileArray) : null;

        if ($cover_fileArray != null) {
            $file_name = $cover_fileArray[0]->file_name;
            Storage::disk('s3')->put('public/user/' . $userID . '/' . 'cover/' . $file_name, base64_decode($cover_fileArray[0]->file), 'public');

            $columnToUpdate = [
                'header_cover' => $file_name,
                'updated_at' => Carbon::now()
            ];

            $profile = User::where('id', $userID)
            ->update($columnToUpdate);

            if ($profile) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $file_name
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

    public function userprofileeditprofile(Request $request) {
        $app_user_id = $request->user_id;

        $new_name = $request->name;
        $new_slug = $request->slug;
        $new_website = $request->website;
        $new_country = $request->country;
        $new_city = $request->city;
        $new_occupation = $request->occupation;
        $new_biography = $request->biography;
        $new_instrument_list = $request->instrument_list != 'none' ? json_decode($request->instrument_list) : null;
        $avatar_fileName = $request->avatar_fileName != 'none' ? $request->avatar_fileName : null;
        $cover_fileName = $request->cover_fileName != 'none' ? $request->cover_fileName : null;

        $columnToUpdate = [
            'name' => $new_name,
            'slug' => $new_slug,
            'website' => $new_website,
            'country' => $new_country,
            'city' => $new_city,
            'profession' => $new_occupation,
            'bio' => $new_biography,
            'instrument' => $new_instrument_list,
            'avatar' => $avatar_fileName,
            'header_cover' => $cover_fileName,
            'updated_at' => Carbon::now()
        ];

        $profile = User::where('id', $app_user_id)
            ->update($columnToUpdate);

        if ($profile) {
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

    public function userprofilegetvisitcount(Request $request) {
        $profile_visit = UserProfileVisitor::where('profile_owner_id', $request->profile_owner_id)->pluck('id')->toArray();

        $user = User::whereIn('id', $profile_visit)->orderBy('name')->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $user
        ], 200);
    }

    public function userprofileinsertvisitcount(Request $request) {
        $visitorID = $request->visitor_id;
        $profileOwnerID = $request->profile_owner_id;

        $exists = UserProfileVisitor::where('visitor_id', $visitorID)
            ->where('profile_owner_id', $profileOwnerID)
            ->count();

        if ($exists == 0) {
            if ($visitorID != $profileOwnerID) {
                $user_visit = new UserProfileVisitor();
                $user_visit->profile_owner_id = $profileOwnerID;
                $user_visit->visitor_id = $visitorID;
                $user_visit->save();

                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $user_visit->toArray()
                ], 200);
            }
        }
    }

    public function usersearchuser(Request $request) {

        if ($request->term != '' && $request->term != null && $request->term != ' ') {
            $user_result = User::where(function($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->term . '%' )
                    ->orWhere('slug', 'like', '%' . $request->term . '%' )
                    ->orWhere('email', 'like', '%' . $request->term . '%' );
                })
                ->get();

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $user_result->toArray()
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }

    public function usergetuserdetailsbyslug(Request $request) {
        $user_details = User::where('slug', $request->slug)->get();
        $user_details_array = [];

        //ADD FOLLOWER, FOLLOWING, UPLOADS LISTS TOMORROW

        if ($user_details->first()) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $user_details->toArray()
            ]);
        } else {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ]);
        }
    }

    public function usergetuserbyid(Request $request) {
        $currentUserID = $request->user_id;

        //Pluck the IDs of the current user's followers
        $followers = UserFollower::where('status', 'accepted')
            ->where('target_user_id', $currentUserID)
            ->pluck('follower_id')->toArray();

        //Pluck the IDs of the current user's followings
        $followings = UserFollower::where('status', 'accepted')
            ->where('follower_id', $currentUserID)
            ->pluck('target_user_id')->toArray();

        //Pluck the IDs of the current user's followers
        $pending_followers = UserFollower::where('status', 'pending')
            ->where('target_user_id', $currentUserID)
            ->pluck('follower_id')->toArray();

        //Pluck the IDs of the current user's followings
        $pending_followings = UserFollower::where('status', 'pending')
            ->where('follower_id', $currentUserID)
            ->pluck('target_user_id')->toArray();

        $followersResult = User::whereIn('id', $followers)->get();
        $followingsResult = User::whereIn('id', $followings)->get();
        $pendingFollowersResult = User::whereIn('id', $pending_followers)->get();
        $pendingFollowingsResult = User::whereIn('id', $pending_followings)->get();

        $user_details_array = [];

        $user_details = User::where('id', $currentUserID)->get();
        $user_details['followers'] = $followersResult;
        $user_details['followings'] = $followingsResult;
        $user_details['pending_followers'] = $pendingFollowersResult;
        $user_details['pending_followings'] = $pendingFollowingsResult;
        $user_details_array[] = $user_details;

        if ($user_details->first()) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $user_details_array
            ]);
        } else {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ]);
        }
    }
}
