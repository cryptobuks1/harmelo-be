<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\UserReview;
use Illuminate\Http\Request;

class UserReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function review(Request $request) {
        $rev = new UserReview();
        $rev->user_id = $request->user_id;
        $rev->reviewer_id = $request->reviewer_id;
        $rev->is_recommended = $request->is_recommended;
        $rev->comment = $request->comment;
        $revsave = $rev->save();

        if (!$revsave) {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => ''
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $rev
        ]);
    }

    public function get(Request $request) {
        $rev = UserReview::where('user_id', $request->user_id)->orderBy('id', 'DESC')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $rev
        ]);
    }

    public function getuserrating(Request $request) {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => UserReview::getUserRating($request->user_id)
        ]);
    }

    public function getpaginated(Request $request) {
        $rev = UserReview::where('user_id', $request->user_id)
            ->orderBy('id', 'DESC')
            ->paginate(3);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $rev
        ]);
    }
}
