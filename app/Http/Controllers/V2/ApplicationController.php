<?php

namespace App\Http\Controllers\V2;

use App\Events\ApplicationSent;
use App\Http\Controllers\Controller;
use App\Jobs\SendApplicationJob;
use App\Jobs\SendApplicationNonMemberJob;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['submitresume', 'submitnonmember']]);
    }

    public function approvenonmember(Request $request) {

        $user_id = $request->user_id;

        $update_application = Application::where('id', $request->id)
        ->update([
            'status' => 'approved',
            'updated_at' => Carbon::now()
        ]);
        if ($update_application) {

            if ($user_id == 'fresh') {
                $explode_name = explode(' ', $request->name);
                $slug_raw = '';
                if (array_key_exists(1, $explode_name)) { //check if name value is single string or string separated by space
                    $slug_raw = implode('-', array(strtolower($explode_name[0]),strtolower($explode_name[1]),time()));
                } else {
                    $slug_raw = implode('-', array(strtolower($explode_name[0]),time()));
                }

                $slug = preg_replace('/\s+/', '', $slug_raw);

                $temporaryPassword = substr(base64_encode(time().$request->name.$request->phone.$request->email), 0, 8);

                $create = new User();
                $create->name = $request->name;
                $create->email = $request->email;
                $create->contact_no = $request->phone;
                $create->is_verified = 1;
                $create->password = Hash::make($temporaryPassword);
                $create->slug = $slug;
                $create->user_type = 'teacher';
                $create->instrument = $request->instrument;
                $create->created_at = Carbon::now();
                $create->updated_at = Carbon::now();

                if ($create->save()) {
                    $user_mail = $request->email;
                    $user_name = $request->name;
                    Mail::send('emails.application-approved',
                    [
                        'receiver'=>$user_name,
                        'receiverEmail'=>$user_mail,
                        'temporaryPassword'=>$temporaryPassword
                    ],
                    function($message) use($user_mail, $user_name) {
                        $message->to($user_mail, $user_name)->subject('Harmelo OnBoarding');
                        $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
                    });

                    return response()->json([
                        'msg' => 'ok',
                        'status' => 1,
                        'data' => []
                    ]);
                }
                return response()->json([
                    'msg' => 'bad 2',
                    'status' => 0,
                    'data' => []
                ]);
            } else {
                $update_user_type = User::where('id', $user_id)
                ->update([
                    'user_type' => 'teacher'
                ]);
                if ($update_user_type) {
                    $user_mail = $request->email;
                    $user_name = $request->name;
                    Mail::send('emails.application-approved-nonfresh',
                    [
                        'receiver'=>$user_name,
                        'receiverEmail'=>$user_mail,
                    ],
                    function($message) use($user_mail, $user_name) {
                        $message->to($user_mail, $user_name)->subject('Harmelo OnBoarding');
                        $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
                    });
                    return response()->json([
                        'msg' => 'ok',
                        'status' => 1,
                        'data' => []
                    ]);
                }
                return response()->json([
                    'msg' => 'bad 2',
                    'status' => 0,
                    'data' => []
                ]);
            }
        }
        return response()->json([
            'msg' => 'bad 1',
            'status' => 0,
            'data' => []
        ]);
    }

    public function approveapplication(Request $request) {
        $update_application = Application::where('user_id', $request->user_id)
            ->update([
                'status' => 'approved',
                'updated_at' => Carbon::now()
            ]);
        if ($update_application) {
            $update_user_type = User::where('id', $request->user_id)
                ->update([
                    'user_type' => 'teacher'
                ]);

            if ($update_user_type) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => []
                ]);
            }
            return response()->json([
                'msg' => 'bad 2',
                'status' => 0,
                'data' => []
            ]);
        }
        return response()->json([
            'msg' => 'bad 1',
            'status' => 0,
            'data' => []
        ]);
    }

    public function getapplications(Request $request) {
        $apps = Application::orderBy('id', 'DESC')->where('status', 'pending')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $apps
        ], 200);
    }

    public function submitnonmember(Request $request) {
        $email_check = User::where('email','=',$request->email)->first();

        request()->validate([
            'resume' => 'required',
            'resume.*' => 'required|mimes:doc,docx,pdf|max:100000'
        ]);
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $filename = preg_replace('/\s/', '', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = $filename.base64_encode(time().$filename).'.'.$file->getClientOriginalExtension();

            $application = new Application();
            if ($email_check) {
                $application->user_id = $email_check->id;
            }
            $application->filename = $filename;
            $application->name = $request->name;
            $application->phone = $request->phone;
            $application->email = $request->email;
            $application->full_address = $request->full_address;
            $application->instruments = $request->instruments;
            $application->created_at = Carbon::now();
            $application->updated_at = Carbon::now();

            if ($application->save()) {
                //SendApplicationNonMemberJob::dispatch($request->name, $request->email, $filename, $file);

                Storage::disk('s3')->put('public/attachment/applications/' . $filename,
                    file_get_contents($file), 'public'
                );

                $user_mail = $request->email;
                $user_name = $request->name;

                Mail::send('emails.applicationv2',
                [
                    'receiver'=>$request->name
                ],
                function($message) use($user_mail, $user_name) {
                    $message->to($user_mail, $user_name)->subject('Harmelo OnBoarding');
                    $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
                });

                //event(new ApplicationSent($user_name));
                broadcast(new ApplicationSent($user_name));
                
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => []
                ], 200);
            }

            return response()->json([
                'msg' => 'ok',
                'status' => 0,
                'data' => []
            ], 200);
        }
    }

    public function submitresume(Request $request) {
        $userID = $request->user_id;
        $userName = $request->user_name;
        $userMail = $request->user_email;

        $attachment_fileArray = $request->attachment !== 'none' ? json_decode($request->attachment) : null;
        $filename = 'none';

        if ($attachment_fileArray != null) {
            $filename = $attachment_fileArray[0]->file_name;
        }

        $is_exist = Application::where('user_id', $userID)->first();

        if ($is_exist == null) {
            $application = new Application();
            $application->user_id = $userID;
            $application->filename = $filename;
            $application->created_at = Carbon::now();
            $application->updated_at = Carbon::now();

            if ($application->save()) {
                if ($filename !== 'none') {
                    SendApplicationJob::dispatch($userName, $userMail, $userID, $filename, $attachment_fileArray[0]->file);
                    return response()->json([
                        'msg' => 'ok',
                        'status' => 1,
                        'data' => []
                    ], 200);
                }
            }
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 0,
            'data' => []
        ], 200);
    }
}
