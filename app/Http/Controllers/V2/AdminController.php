<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\BookingRevenue;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getUnassignedRequests(Request $request) {
        $list = Withdrawal::where('assigned_to', null)->where('status', 'pending')->orderBy('id', 'DESC')->get();

        $arr = [];

        foreach($list as $l) {
            $l['requested_by'] = User::where('id', $l->user_id)->first();
            $arr[] = $l;
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $arr
        ]);
    }

    public function getRequestByStatus(Request $request) {
        $list = Withdrawal::where('status',  $request->status)->orderBy('id', 'DESC')->get();

        $arr = [];

        foreach($list as $l) {
            $l['requested_by'] = User::where('id', $l->user_id)->first();
            $arr[] = $l;
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $arr
        ]);
    }

    public function proccessRequest(Request $request) {
        $update = Withdrawal::where('id', $request->request_id)
            ->update([
                'assigned_to' => $request->assigned_to,
                'status' => 'in-progress',
                'updated_at' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
        }

        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ]);
    }

    public function cancelRequest(Request $request) {
        $update = Withdrawal::where('id', $request->request_id)
            ->update([
                'status' => 'cancelled',
                'updated_at' => Carbon::now()
            ]);

        if ($update) {
            $revenue = floatval(BookingRevenue::where('user_id', $request->user_id)->first()->total_revenue);
            BookingRevenue::where('user_id', $request->user_id)
                ->update([
                    'total_revenue' => $revenue + floatval(strval($request->amount_request)),
                    'updated_at' => Carbon::now()
                ]);

            if ($revenue) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => []
                ]);
            }
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ]);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ]);
    }

    public function completeRequest(Request $request) {
        $update = Withdrawal::where('id', $request->request_id)
            ->update([
                'status' => 'completed',
                'updated_at' => Carbon::now()
            ]);

        if ($update) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
        }

        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ]);
    }
}
