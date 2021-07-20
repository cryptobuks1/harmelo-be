<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use App\Models\LessonBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LessonBookingController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);

        //$this->middleware('cors');
    }

    /**
     * Schedule Status legend:
     * 0 = pending;
     * 1 = approved;
     * 2 = declined;
     * 3 = user cancelled
    */

    public function updatebookingstatus(Request $request) {
        $booking_status = $request->booking_status;
        $status_name = 'Approved';

        /**
         * Rule:
         * In this function, the enrollee is always the mail receiver
         * Except, when the enrollees cancels their reservation, then the instructor becomes the mail receiver
         */
        $receiverName = $request->enrollee_name;
        $receiverEmail = $request->enrollee_email;
        $receiverSlug = $request->enrollee_slug;

        $thirdPersonName = $request->instructor_name;
        $thirdPersonEmail = $request->instructor_email;
        $thirdPersonSlug = $request->instructor_slug;

        if ($booking_status == 2) {
            $status_name = 'Declined';
        }
        if ($booking_status == 3) {
            $status_name = 'Cancelled';

            $receiverName = $request->instructor_name;
            $receiverEmail = $request->instructor_email;
            $receiverSlug = $request->instructor_slug;

            $thirdPersonName = $request->enrollee_name;
            $thirdPersonEmail = $request->enrollee_email;
            $thirdPersonSlug = $request->enrollee_slug;
        }
        if ($booking_status == 4) {
            $status_name = 'Cancelled';
        }

        $scheduleID = $request->schedule_id;
        $scheduleDate = $request->schedule_date;
        $schedulTime = $request->schedule_time;
        $scheduleInstrument = $request->schedule_instrument !== 'null' ? $request->schedule_instrument : 'N/A';

        $actionMessage = $request->approve_message;

        /*$instructorName = $request->instructor_name;
        $instructorEmail = $request->instructor_email;
        $instructorSlug = $request->instructor_slug;

        $enrolleeName = $request->enrollee_name;
        $enrolleeEmail = $request->enrollee_email;
        $enrolleeSlug = $request->enrollee_slug;*/

        $schedule = LessonBooking::where('id', $scheduleID)
            ->update(['status'=>$booking_status, 'updated_at'=>Carbon::now()]);

        if ($schedule) {

            Mail::send('emails.enrollment-updatestatus',
                [
                    'receiver'=>$receiverName, 'set_date'=>$scheduleDate, 'set_time'=>$schedulTime,
                    'enrollee_slug'=>$receiverSlug, 'enrollee_name'=>$receiverName,
                    'instructor_slug'=>$thirdPersonSlug, 'instructor_name'=>$thirdPersonName,
                    'instrument_name'=>$scheduleInstrument, 'action_message'=>$actionMessage,
                    'booking_status'=>$booking_status
                ],
                function($message) use($receiverEmail, $receiverName, $status_name) {
                    $message->to($receiverEmail, $receiverName)->subject($status_name . ' lesson schedule');
                    $message->from('support@harmelo.com','Harmelo MusicED');
            });

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 0,
            'data' => []
        ]);
    }

    public function getuserbookingschedule(Request $request) {
        $userID = $request->user_id;

        $schedule = LessonBooking::where('enrollee_id', $userID)
            ->where('is_delete', 0)
            ->where('status', '<', 2)
            ->get();

        $renamed_result_set = [];
        if ($schedule->count() > 0) {

            $map = $schedule->map(function ($item, $key) {
                $item['instrument_name'] = $item->instrument_id != null ? Instrument::getInstrumentByID($item->instrument_id)[0]->instrument_name : null;
                $item['instructor_name'] = User::getUserDetailsByID($item->instructor_id)[0]->name;
                $item['instructor_email'] = User::getUserDetailsByID($item->instructor_id)[0]->email;
                $item['instructor_slug'] = User::getUserDetailsByID($item->instructor_id)[0]->slug;

                $item['enrollee_name'] = User::getUserDetailsByID($item->enrollee_id)[0]->name;
                $item['enrollee_email'] = User::getUserDetailsByID($item->enrollee_id)[0]->email;
                $item['enrollee_slug'] = User::getUserDetailsByID($item->enrollee_id)[0]->slug;

                $item['name'] = explode('-',$item->time)[0];
                $item['start'] = $item->ddate . ' ' . explode('-',$item->time)[0];
                $item['end'] = $item->ddate . ' ' . explode('-',$item->time)[1];
                return $renamed_result_set = $item;
            });

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $map
            ], 200);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function getinstructorschedule(Request $request) {
        $instructorID = $request->instructor_id;

        $schedule = LessonBooking::where('instructor_id', $instructorID)
            ->where('is_delete', 0)
            ->where('status', '<', 2)
            ->get();

        $renamed_result_set = [];
        if ($schedule->count() > 0) {

            $map = $schedule->map(function ($item, $key) {
                $item['instrument_name'] = $item->instrument_id != null ? Instrument::getInstrumentByID($item->instrument_id)[0]->instrument_name : null;
                $item['instructor_name'] = User::getUserDetailsByID($item->instructor_id)[0]->name;
                $item['instructor_email'] = User::getUserDetailsByID($item->instructor_id)[0]->email;
                $item['instructor_slug'] = User::getUserDetailsByID($item->instructor_id)[0]->slug;

                $item['enrollee_name'] = User::getUserDetailsByID($item->enrollee_id)[0]->name;
                $item['enrollee_email'] = User::getUserDetailsByID($item->enrollee_id)[0]->email;
                $item['enrollee_slug'] = User::getUserDetailsByID($item->enrollee_id)[0]->slug;

                $item['name'] = explode('-',$item->time)[0];
                $item['start'] = $item->ddate . ' ' . explode('-',$item->time)[0];
                $item['end'] = $item->ddate . ' ' . explode('-',$item->time)[1];
                return $renamed_result_set = $item;
            });

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $map
            ], 200);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function createschedule(Request $request) {
        $instructorID = $request->instructor_id;
        $enrolleeID = $request->enrollee_id;
        $ddate = $request->ddate;
        $timeslot = strpos($request->timeslot, ',') !== false ? explode(',', $request->timeslot) : [$request->timeslot];
        $instrumentID = $request->instrument_id;

        /**
         * Recepients
         */
        $instructorEmail = $request->instructor_email;
        $instructorName = $request->instructor_name;
        $instructorSlug = $request->instructor_slug;

        $enrolleeEmail = $request->enrollee_email;
        $enrolleeName = $request->enrollee_name;
        $enrolleeSlug = $request->enrollee_slug;

        $arrays = [];
        foreach($timeslot as $slot) {
            $booking = new LessonBooking();
            $booking->instructor_id = $instructorID;
            $booking->enrollee_id = $enrolleeID;
            $booking->ddate = $ddate;
            $booking->time = $slot;
            $booking->instrument_id = $instrumentID != 'none' ? $instrumentID : null;
            $booking->created_at = Carbon::now();
            $booking->updated_at = Carbon::now();
            $booking->save();

            $arrays[] = $booking;
        }

        $map = array_map(function($item) {
            $item['instrument_name'] = $item->instrument_id != null ? Instrument::getInstrumentByID($item->instrument_id)[0]->instrument_name : null;

            $item['instructor_name'] = User::getUserDetailsByID($item->instructor_id)[0]->name;
            $item['instructor_email'] = User::getUserDetailsByID($item->instructor_id)[0]->email;
            $item['instructor_slug'] = User::getUserDetailsByID($item->instructor_id)[0]->slug;

            $item['enrollee_name'] = User::getUserDetailsByID($item->enrollee_id)[0]->name;
            $item['enrollee_email'] = User::getUserDetailsByID($item->enrollee_id)[0]->email;
            $item['enrollee_slug'] = User::getUserDetailsByID($item->enrollee_id)[0]->slug;

            $item['name'] = explode('-',$item->time)[0];
            $item['start'] = $item->ddate . ' ' . explode('-',$item->time)[0];
            $item['end'] = $item->ddate . ' ' . explode('-',$item->time)[1];
            return $renamed_result_set = $item;
        }, $arrays);

        if(!empty($map)) {
            $instrumentName = 'N/A';
            if (!empty(Instrument::getInstrumentByID($instrumentID))) {
                $instrumentName = Instrument::getInstrumentByID($instrumentID)[0]->instrument_name;
            }

            //Send mail to instructor
            Mail::send('emails.enrollment-instructor',
                [
                    'receiver'=>$instructorName, 'set_date'=>$ddate, 'set_time'=>$timeslot,
                    'enrollee_slug'=>$enrolleeSlug, 'enrollee_name'=>$enrolleeName,
                    'instrument_name'=>$instrumentName
                ],
                function($message) use($instructorEmail, $instructorName) {
                    $message->to($instructorEmail, $instructorName)->subject('You have a new enrollment reservation!');
                    $message->from('support@harmelo.com','Harmelo MusicED');
            });

            //Send mail to enrollee
            Mail::send('emails.enrollment-enrollee',
                [
                    'receiver'=>$enrolleeName, 'set_date'=>$ddate, 'set_time'=>$timeslot,
                    'instructor_slug'=>$instructorSlug, 'instructor_name'=>$instructorName,
                    'instrument_name'=>$instrumentName
                ],
                function($message) use($enrolleeEmail, $enrolleeName) {
                    $message->to($enrolleeEmail, $enrolleeName)->subject('You have successfully booked a lesson session!');
                    $message->from('support@harmelo.com','Harmelo MusicED');
            });

            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $map
            ], 200);
        }

        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ], 200);

    }
}
