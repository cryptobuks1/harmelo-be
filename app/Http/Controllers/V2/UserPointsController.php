<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\UserPoints;
use Illuminate\Http\Request;

class UserPointsController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getuserpoints(Request $request) {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => UserPoints::where('user_id', $request->user_id)->get()->toArray()
        ]);
    }
}
