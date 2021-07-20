<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\WorkExperience;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request) {
        $exp = new WorkExperience();
        $exp->user_id = $request->user_id;
        $exp->company = $request->company;
        $exp->job_description = $request->job_description;
        $exp->location = $request->location;
        $exp->date_from = $request->date_from;
        $exp->date_to = $request->date_to;
        $save = $exp->save();

        if (!$save) {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => ''
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $exp
        ]);
    }

    public function get(Request $request) {
        $rev = WorkExperience::where('user_id', $request->user_id)->orderBy('id', 'DESC')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $rev
        ]);
    }
}
