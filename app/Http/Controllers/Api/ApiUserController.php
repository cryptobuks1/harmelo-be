<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoachingApplication;
use App\Models\CoachInstrumentRate;
use App\Models\Instrument;
use App\Models\User;
use App\Models\UserCV;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ApiUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }

    public function getuserinstrumentsandrates(Request $request) {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => CoachInstrumentRate::where('user_id', $request->instructor_id)->with('instrument')->get()
        ]);
    }

    public function getpendingapplications(Request $request) {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => CoachingApplication::where('status', 0)->with('user')->get()
        ], 200);
    }

    public function changeapptheme(Request $request) {
        $currentUserID = $request->user_id;
        $newTheme = $request->theme;

        $update = User::where('id', $currentUserID)
            ->update(['app_theme'=>$newTheme]);

        if ($update) {
            return response()->json([
                'msg'=>'ok',
                'status'=>1
            ]);
        }
        return response()->json([
            'msg'=>'ok',
            'status'=>0
        ]);
    }

    public function isusercoachingapplied(Request $request) {
        $userID = $request->user_id;
        $is_applied = CoachingApplication::where('user_id', $userID)->first();


        if ($is_applied) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1
            ]);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 0
        ]);
    }

    public function submitcoachingapplication(Request $request) {
        $userID = $request->user_id;
        $userName = $request->user_name;
        $userEmail = $request->user_email;
        $userSlug = $request->user_slug;
        $instrumentRate = $request->instrument_rate;
        $applicantName = $request->applicant_name;

        $attachment_fileArray = $request->attachment !== 'none' ? json_decode($request->attachment) : null;
        $filename = 'none';

        if ($attachment_fileArray != null) {
            $filename = $attachment_fileArray[0]->file_name;
        }

        $is_exist = CoachingApplication::where('user_id', $userID)->first();

        if ($is_exist == null) {
            $application = new CoachingApplication();
            $application->user_id = $userID;
            $application->applicant_name = $applicantName;
            $application->attachment = $filename;
            $application->instrument_rate = $instrumentRate;

            if ($application->save()) {
                if ($filename !== 'none') {
                    Storage::disk('s3')->put('public/user/' . $userID . '/attachment/' . $filename,
                        base64_decode($attachment_fileArray[0]->file), 'public'
                    );
                }


                Mail::send('emails.application-coach',
                [
                    'receiver'=>$userName,
                    'receiver_slug'=>$userSlug
                ],
                function($message) use($userEmail, $userName, $filename) {
                        $message->to($userEmail, $userName)->subject('Harmelo Coaching Team - Application!');
                        $message->from('support@harmelo.com','Harmelo MusicED');
                        if ($filename !== 'none') {
                            $message->attach('https://s3-ap-southeast-1.amazonaws.com/storage.harmelo-staging.com/public/user/2/attachment/c5355856f02ab3a749ad03b2d9da18b5.pdf',
                            [
                                'as' => $filename
                            ]);
                        }
                });

                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => []
                ], 200);
            }
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 0,
            'data' => []
        ], 200);
    }

    public function editusercv(Request $request) {
        $userID = $request->user_id;
        $cvObject = $request->cv_object;

        $usercv = UserCV::where('user_id', $userID)
            ->update(['cv'=> $cvObject, 'updated_at'=>Carbon::now()]);

        if ($usercv) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
        }
        return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ]);
    }

    public function submitusercv(Request $request) {
        $userID = $request->user_id;
        $cvObject = $request->cv_object;

        $is_exist = UserCV::where('user_id', $userID)
            ->where('is_delete', 0)
            ->get();

        if ($is_exist->count() > 0) {
            return response()->json([
                'msg' => 'ok',
                'status' => 0,
                'data' => []
            ]);
        } else {
            $cv = new UserCV();
            $cv->user_id = $userID;
            $cv->cv = $cvObject;
            $cv->created_at = Carbon::now();
            $cv->updated_at = Carbon::now();

            if ($cv->save()) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $cv
                ]);
            } else {
                return response()->json([
                    'msg' => 'bad',
                    'status' => -1,
                    'data' => []
                ]);
            }
        }
    }

    public function usergetuserdetailsbyslug(Request $request) {
        $user_details = User::where('slug', $request->slug)->get();
        $user_details_array = [];

        if ($user_details->first()) {
            $user_cv = UserCV::getCVByUserID($user_details[0]->id);
            if ($user_cv->count() > 0) {
                $user_details[0]['cv'] = $user_cv[0]->cv;
            }


            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $user_details->toArray()
            ]);
        } else {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ]);
        }
    }

    public function getallteacher() {
        $user = User::where('user_type', 'teacher')
            ->get();

        if ($user->count() > 0) {
            $arrays = [];
            foreach ($user as $u) {
                $instrument_ids = str_replace(array('[',']'), '',$u->instrument);
                $u['instrument_list'] = Instrument::getInstrumentByID($instrument_ids);
                $arrays[] = $u;
            }
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $arrays
            ], 200);
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 0,
            'data' =>[]
        ], 200);
    }
}
