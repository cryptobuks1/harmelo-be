<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailVerify;
use App\Models\User;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Spatie\GoogleCalendar\GoogleCalendar;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthController extends Controller
{

    public function invalidateaccesscode(Request $request) {
        User::where('id', $request->user_id)
            ->update(['access_code' => null]);
    }

    public function getuserbyaccesscode(Request $request) {
        $access_code = $request->access_code;
        $user = User::where('access_code', $access_code)->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $user->toArray()
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function loginnative(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 0,
                'msg' => $validator->errors()->all(), //get error for all fields
                'data' => [],
                'token' => ''
            ], 200);

        } else {

            $loginData = [
                'email' => $request->email,
                'password' => $request->password
            ];

            if ( !auth()->attempt($loginData) ) {

                return response()->json([
                    'status' => 0,
                    'msg' => 'Invalid credentials.',
                    'data' => [],
                    'token' => ''
                ], 200);

            } else {

                $user = auth()->user();

                User::where('id', $user->id)
                    ->where('email', $user->email)
                    ->update(['access_code' => md5(time() . $user->name . $user->slug)]);

                Auth::login($user);

                $accessToken = $user->createToken($user->email.'-'.now())->accessToken;

                return response()->json([
                    'status' => 1,
                    'msg' => 'ok',
                    'data' => $user,
                    'token' => $accessToken
                ], 200);

            }

        }

    }

    public function validateverificationcode(Request $request) {
        $userID = $request->user_id;
        $userEmail = $request->email;
        $otpCode = $request->otp;
        $minutes_to_add = 5;

        $exist = EmailVerify::where('is_used', 0)
            ->where('user_id', $userID)
            ->where('email', $userEmail)
            ->where('verification_code', $otpCode)
            ->get();

        if ($exist->first()) {
            $codeCreated = new DateTime($exist[0]->created_at);
            $codeExpires = new DateTime();

            if (date_diff($codeCreated, $codeExpires)->i >= 5) { //Code expires at 5mins
                return response()->json([
                    'msg' => 'otp expired',
                    'status' => -2,
                    'data' => []
                ], 200);
            } else {
                $code_verified = EmailVerify::where('is_used', 0)
                    ->where('user_id', $userID)
                    ->where('email', $userEmail)
                    ->where('verification_code', $otpCode)
                    ->update(['is_used' => 1]);

                if ($code_verified) {
                    $verifiy_user = User::where('id', $userID)
                        ->update(['is_verified' => 1]);

                    if ($verifiy_user) {
                        return response()->json([
                            'msg' => 'ok',
                            'status' => 1,
                            'data' => []
                        ], 200);
                    } else {
                        return response()->json([
                            'msg' => 'user not verified',
                            'status' => 0,
                            'data' => []
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'msg' => 'code not updated',
                        'status' => 0,
                        'data' => []
                    ], 200);
                }
            }
        } else {
            return response()->json([
                'msg' => 'code not exist',
                'status' => 0,
                'data' => []
            ], 200);
        }
    }

    public function insertnewverificationcode(Request $request) {

        $userID = $request->user_id;
        $userEmail = $request->email;
        $receiverName = $request->receiver_name;

        $milliseconds = round(microtime(true) * 1000);

        $old_verification = EmailVerify::where('is_used', 0)
            ->where('user_id', $userID)
            ->where('email', $userEmail)
            ->get();

        if ($old_verification->first()) {
            $invalidate_old_verification = EmailVerify::where('is_used', 0)
            ->where('user_id', $userID)
            ->where('email', $userEmail)
            ->where('verification_code', $old_verification[0]->verification_code)
            ->update(['is_used' => 1]);

            if ($invalidate_old_verification) {

                $otpCode = substr($milliseconds, 7);

                $verification = new EmailVerify();
                $verification->user_id = $userID;
                $verification->email = $userEmail;
                $verification->verification_code = $otpCode;

                $saved = $verification->save();

                if ($saved) {
                    Mail::send('emails.test', ['receiver'=>$receiverName, 'verification_code'=>$otpCode], function($message) use($otpCode, $userEmail, $receiverName) {
                        $message->to($userEmail, $receiverName)->subject('Your OTP is ' . $otpCode);
                    });
                    return response()->json([
                        'msg' => 'ok',
                        'status' => 1,
                        'data' => []
                    ]);
                }
            } else {
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ]);
            }
        } else {
            $otpCode = substr($milliseconds, 7);

            $verification = new EmailVerify();
            $verification->user_id = $userID;
            $verification->email = $userEmail;
            $verification->verification_code = $otpCode;

            $saved = $verification->save();

            if ($saved) {
                Mail::send('emails.test', ['receiver'=>$receiverName, 'verification_code'=>$otpCode], function($message) use($otpCode, $userEmail, $receiverName) {
                    $message->to($userEmail, $receiverName)->subject('Your OTP is ' . $otpCode);
                });
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ]);
            }
        }

    }

    public function registernative(Request $request) {

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|max:55',
            'lastname' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed|min:8'
        ]);

        $firstName = $request->firstname;
        $lastName = $request->lastname;
        $fullName = $firstName . ' ' . $lastName;
        $userEmail = $request->email;
        $userPassword = $request->password;

        if ($validator->fails()) {

            return response()->json([
                'status' => 0,
                'msg' => $validator->errors()->all(), //get error for all fields
                'data' => [],
                'token' => ''
            ], 401);

        } else {
            $slug_raw = implode('-', array(strtolower($firstName),strtolower($lastName),time()));
            $slug = preg_replace('/\s+/', '', $slug_raw);

            $user = new User();
            $user->name = $fullName;
            $user->email = $userEmail;
            $user->slug = $slug;
            $user->password = Hash::make($userPassword);

            $user_save = $user->save();

            if ($user_save) {
                $accessToken = $user->createToken($user->email.'-'.now())->accessToken;

                $milliseconds = round(microtime(true) * 1000);

                $otpCode = substr($milliseconds, 7);

                $verification = new EmailVerify();
                $verification->user_id = $user->id;
                $verification->email = $user->email;
                $verification->verification_code = $otpCode;

                $verification->save();

                Mail::send('emails.test', ['receiver'=>$firstName . ' ' . $lastName, 'verification_code'=>$otpCode], function($message) use($otpCode, $userEmail, $fullName) {
                    $message->to($userEmail, $fullName)->subject('Your OTP is ' . $otpCode);
                });

                return response()->json([
                    'status' => 1,
                    'msg' => 'ok',
                    'data' => $user->toArray(),
                    'token' => $accessToken
                ], 200);
            }
        }
    }


}
