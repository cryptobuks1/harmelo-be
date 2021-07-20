<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Events;
use App\Models\EventsClient;
use App\Models\EventsRecurring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class EventsController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
    }

    public function add(Request $request) {
        $status = Events::add($request->user_id, $request);
        return response()->json([
            'msg' => 'ok',
            'status' => $status,
            'data' => []
        ]);
    }
    public function addClass(Request $request) {
        $status = Events::addClass($request->user_id, $request);
        return response()->json([
            'msg' => 'ok',
            'status' => $status,
            'data' => []
        ]);
    }
    public function get(Request $request) {
        $list = Events::where('user_id', $request->user_id)->where('is_delete', 0)
            ->with('notes')
            ->with('appointments')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function getEventsByPastOrUpcoming(Request $request) {
        $where = '';
        if ($request->type == 1)
             $where = 'CONCAT(event_date, " ", time_start) >= now()';
        else
            $where = 'CONCAT(event_date, " ", time_start) < now()';

        $list = Events::where('user_id', $request->user_id)->where('is_delete', 0)
            ->whereRaw($where)
            ->with('notes')
            ->with('appointments')->orderBy('id', 'desc')->get();

    
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }

    public function getEventByID(Request $request) {
        $list = Events::where('slug_url', $request->slug)
                        ->where('id', $request->id)->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }

    public function getEventsByTeacher(Request $request) {
        $list = Events::where('user_id', $request->user_id)
            ->where('event_type', 'class')
            ->where('is_published', 1)
            ->where('is_delete', 0)
            ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
            ->get();

        $events = [];
        foreach($list as $l) {
            $l['clients'] = EventsClient::getClientsByEvent($l->id);
            $l['recurring_status'] = EventsRecurring::getRecurringStatusByEvent($l->id);
            $l['is_booked'] = Appointments::getAppointmentStatus($request->student_id, $l->id);
            $l['slots_available'] = Appointments::getLostsAvailable($l->id, $l->spaces);
            $events[] = $l;
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $events
        ]);
    }
    public function getRecurringClass(Request $request) {
        $list = Events::where('user_id', $request->user_id)
            ->where('parent_id', $request->event_id)
            ->where('is_published', 1)
            ->where('is_delete', 0)
            ->with('user')
            ->with('appointments')
            ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
            ->get();
        
        $events = [];
        foreach($list as $l) {
            if ($l->event_type == 'class') {
               $l['is_booked'] = Appointments::getAppointmentStatus($request->student_id, $l->id);
            }  else {
                
            }

            $l['slots_available'] = Appointments::getLostsAvailable($l->id, $l->spaces);
            $events[] = $l;
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $events
        ]);
    }
    public function getApppointmentListByTeacher(Request $request) {
        $list = Events::where('event_type', 'appt')
            ->where('user_id', '=', $request->user_id)
            ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
            ->where('is_published', 1)->get();

        $events = [];
        foreach($list as $l) {
            $l['clients'] = EventsClient::getClientsByEvent($l->id);
            $l['recurring_status'] = EventsRecurring::getRecurringStatusByEvent($l->id);
            $events[] = $l;
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $events
        ]);
    }
    public function getApppointmentList(Request $request) {
        $list = Events::where('event_type', 'appt')
                ->where('is_delete', 0)
                ->with('appointments')
                ->with('user')
                ->whereColumn('id', 'parent_id')->where('is_published', 1)->get();
                /* 
                ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')*/

        $events = [];
        foreach($list as $l) {
            $l['clients'] = EventsClient::getClientsByEvent($l->id);
            $l['recurring_status'] = EventsRecurring::getRecurringStatusByEvent($l->id);
            $l['is_available'] = Events::checkIfAvailable($l->id);
            $events[] = $l;
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $events
        ]);
    }
    public function getClass(Request $request) {
        $list = Events::where('event_type', 'class')
                ->where('is_delete', 0)
                ->with('appointments')
                ->with('user')
                ->whereColumn('id', 'parent_id')
                ->where('is_published', 1)->get();

        $events = [];
        foreach($list as $l) {
            $l['clients'] = EventsClient::getClientsByEvent($l->id);
            $l['recurring_status'] = EventsRecurring::getRecurringStatusByEvent($l->id);
            $l['is_available'] = Events::checkIfAvailable($l->id);
            $events[] = $l;
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $events
        ]);
    }


    public function getAppointmentByTeacher(Request $request) {
        $data = Appointments::where('user_id', $request->user_id)->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $data
        ]);
    }
    public function delete(Request $request) {
        if ($request->type == 'current') {
            Events::where('id', $request->id)->update([
                'is_delete' => 1
            ]);
        } else {
            $parent_id = Events::getParentID($request->id);
            Events::where('id', $request->id)->update([
                'is_delete' => 1
            ]);
            Events::where('id', '>', $request->id)
                    ->where('parent_id', $parent_id)->update(['is_delete' => 1]);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }

    public function published(Request $request) {
        if ($request->type == 'current') {
            Events::where('id', $request->id)->where('is_delete', 0)->update([
                'is_published' => 1
            ]);
        } else {
            $parent_id = Events::getParentID($request->id);
            Events::where('id', $request->id)->where('is_delete', 0)->update([
                'is_published' => 1
            ]);
            Events::where('id', '>', $request->id)
                    ->where('parent_id', $parent_id)->where('is_delete', 0)->update(['is_published' => 1]);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
    public function unPublished(Request $request) {
        if ($request->type == 'current') {
            Events::where('id', $request->id)->where('is_delete', 0)->update([
                'is_published' => 0
            ]);
        } else {
            $parent_id = Events::getParentID($request->id);
            Events::where('id', $request->id)->where('is_delete', 0)->update([
                'is_published' => 0
            ]);
            Events::where('id', '>', $request->id)
                    ->where('parent_id', $parent_id)->where('is_delete', 0)->update(['is_published' => 0]);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]); 
    }


}
