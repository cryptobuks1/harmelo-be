<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function get(Request $request) {
        $list = ActivityLogs::where('user_id', $request->user_id)->orderBy('id', 'desc')->get();

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $list
            ]);
    }

}
