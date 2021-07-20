<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Events;
use App\Models\EventsClient;
use App\Models\EventsRecurring;
use App\Models\Instrument;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getpendingapplicationlist() {
        $list = Listing::getPendingApplicationList();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }

    public function getEventsByTeacher($user_id, $student_id) {
        $list = Events::where('user_id', $user_id)
            ->where('event_type', 'class')
            ->where('is_published', 1)
            ->where('is_delete', 0)
            ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
            ->get();

        $events = [];
        foreach($list as $l) {
            $l['clients'] = EventsClient::getClientsByEvent($l->id);
            $l['recurring_status'] = EventsRecurring::getRecurringStatusByEvent($l->id);
            $l['is_booked'] = Appointments::getAppointmentStatus($student_id, $l->id);
            $l['slots_available'] = Appointments::getLostsAvailable($l->id, $l->spaces);
            $events[] = $l;
        }

        return $events;
    }

    public function teacherlist(Request $request) {
        $teacher = User::where('user_type', 'teacher')->where('id', '!=', Auth::user()->id)
            ->get();

            $array = [];

            foreach($teacher as $t) {
                $t['events'] = $this->getEventsByTeacher($t->id, $request->student_id);
                $t['reviews'] = Listing::getTeacherReview($t->id);
                $array[] = $t;
            }

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $array//$teacher
            ]);
    }

    public function instrumentlist() {
        $list = Instrument::where('is_delete', 0)->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
}
