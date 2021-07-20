<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\BookingRevenue;
use App\Models\User;
use App\Models\VerificationCodes;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
    }

    public function cancelRequest(Request $request) {
        $update = Withdrawal::where('id', $request->id)
            ->update([
                'status' => 'cancelled',
                'updated_at' => Carbon::now()
            ]);
        if ($update) {
            $revenue = floatval(BookingRevenue::where('user_id', $request->user_id)->first()->total_revenue);
            BookingRevenue::where('user_id', $request->user_id)
                ->update([
                    'total_revenue' => $revenue + floatval(strval($request->amount)),
                    'updated_at' => Carbon::now()
                ]);
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
        }
    }

    public function submitRequest(Request $request) {
        $submit = new Withdrawal();
        $submit->user_id = $request->user_id;
        $submit->amount_request = strval($request->amount);
        $submit->desription = $request->description;
        $submit->status = 'pending';
        $submit->created_at = Carbon::now();
        $submit->updated_at = Carbon::now();

        if ($submit->save()) {
            $revenue = floatval(BookingRevenue::where('user_id', $request->user_id)->first()->total_revenue);
            BookingRevenue::where('user_id', $request->user_id)
                ->update([
                    'total_revenue' => $revenue - floatval(strval($request->amount)),
                    'updated_at' => Carbon::now()
                ]);
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => BookingRevenue::where('user_id', $request->user_id)->first()
            ]);
        }
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => []
        ]);
    }

    public function getRequestByUser(Request $request) {
        $list = [];
        if ($request->status == 'all') {
            $list = Withdrawal::where('user_id', $request->user_id)->orderBy('id', 'DESC')->get();
        } else {
            $list = Withdrawal::where('user_id', $request->user_id)->where('status', $request->status)->orderBy('id', 'DESC')->get();
        }
        $arr = [];
        foreach($list as $l) {
            $l['user'] = User::where('id', $l->user_id)->first();
            $l['agent'] = User::where('id', $l->assigned_to)->first();
            $arr[] = $l;
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $arr
        ]);
    }
    public function getRequestByAdmin(Request $request) {
        $list = Withdrawal::where('assigned_to', $request->assigned_to)->where('status', 'in-progress')->orderBy('id', 'DESC')->get();

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

    public function createVerif(Request $request) {
        $code = substr(md5(time().'.'.$request->user_id.'.'.$request->codetype), -6);
        $verif = new VerificationCodes();
        $verif->code = $code;
        $verif->created_at = Carbon::now();
        $verif->updated_at = Carbon::now();

        $user_mail = $request->user_mail;
        $user_name = $request->user_name;

        Mail::send('emails.verifcode',
        [
            'receiver'=>$user_name,
            'verifCode' => $code
        ],
        function($message) use($user_mail, $user_name) {
            $message->to($user_mail, $user_name)->subject('Verification');
            $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });

        $verif->save();
    }

    public function verifyCode(Request $request) {
        $verif = VerificationCodes::where('code', $request->code)->where('status', 0)->first();

        if (!$verif) {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
        ]);
    }
}
