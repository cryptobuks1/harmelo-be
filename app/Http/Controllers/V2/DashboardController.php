<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\ActivityLogs;
use App\Models\Appointments;
use App\Models\BookingRevenue;
use App\Models\Contacts;
use App\Models\ProfileVisit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Profiler\Profile;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getData(Request $request) {
        $list['appoinments'] =  Appointments::where('user_id', $request->user_id)->where('event_type', 'appt')->where('status', 1)->where('is_delete', 0)->count();
        $list['class'] =  Appointments::where('user_id', $request->user_id)->where('event_type', 'class')->where('status', 1)->where('is_delete', 0)->count();
        $list['clients'] =  Contacts::where('user_id', $request->user_id)->where('is_delete', 0)->count();
        $list['revenue'] =  DB::table("tbl_appointments")->get()->sum("price");
        $list['withdrawable_earnings'] = BookingRevenue::where('user_id', $request->user_id)->get();
        $list['pending_withdrawals'] = Withdrawal::where('user_id', $request->user_id)->where('status', 'pending')->where('is_delete', 0)->count();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function getAppointmentList(Request $request) {
        $where = '';
        if ($request->type == 1)
             $where = 'CONCAT(event_date, " ", time_start) >= now()';
        else
            $where = 'CONCAT(event_date, " ", time_start) < now()';
    

        $list = Appointments::where('user_id', $request->user_id)
                ->whereRaw($where)
                ->where('status', 1)
                ->with('notes')
                ->with('events')
                ->with('user')
                ->with('client')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    
    public function getActivityLogs(Request $request) {
        $list = ActivityLogs::where('user_id', $request->user_id)->orderBy('id', 'desc')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }


}
