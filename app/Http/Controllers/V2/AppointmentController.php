<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Events;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => ['getAppointmentByClient']]);
    }

    public function getAppointmentByClient(Request $request) {
        $where = '';
        if ($request->type == 1)
             $where = 'CONCAT(event_date, " ", time_start) >= now()';
        else
            $where = 'CONCAT(event_date, " ", time_start) < now()';

        $list = Appointments::where('client_id', $request->user_id)
                ->whereRaw($where)
                ->with('notes')
                ->with('events')
                ->with('user')
                ->orderBy('id', 'desc')
                ->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function getAppointmentByTeacher(Request $request) {
        $where = '';
        if ($request->type == 1)
             $where = 'CONCAT(event_date, " ", time_start) >= now()';
        else
            $where = 'CONCAT(event_date, " ", time_start) < now()';
    
;
        $list = Appointments::where('user_id', $request->user_id)
                ->whereRaw($where)
                ->with('notes')
                ->with('events')
                ->with('user')
                ->with('client')
                ->orderBy('id', 'desc')
                ->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function bookClass(Request $request) {
        $event = Events::where('id', $request->event_id)->first();
        $status = Appointments::bookClass($request->user_id, $request, $event);
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
    public function bookAppointments(Request $request) {
        $event = Events::where('id', $request->event_id)->first();
        $status = Appointments::book($request->user_id, $request, $event);

        return response()->json([
            'msg' => 'ok',
            'status' => $status,
            'data' => []
        ]);
    }
    public function approvedAppointments(Request $request) {
        $status = Appointments::where('id', $request->id)->update(['status' => 1]);
        Appointments::sendApprovedEmail($request);
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
    public function sendReminder(Request $request) {
        Appointments::sendReminderEmail($request);
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
    public function disApprovedAppointments(Request $request) {
        $status = Appointments::where('id', $request->id)->update(['status' => -1]);
        Appointments::sendDisApprovedEmail($request);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
    public function reconcileAppointments(Request $request) {
        $status = Appointments::where('id', $request->id)->update([
                'action_taken' => $request->attendance,
                'is_reconciled' => 1]);

        Appointments::reconcileLogs($request);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
}
