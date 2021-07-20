<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RegisterVerification;
use App\Models\Coins;
use App\Models\EmailVerify;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthControllerV2 extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => ['loginuser', 'verifyemail', 'registeruser', 'registerviaprovider', 'apiautologin', 'redirectToGoogle', 'handleGoogleCallback', '_registerOrLoginUser', 'loginviaprovider']]);

        $this->middleware('cors');
    }

    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'msg' => $validator->errors()->all(),
                'status' => 0,
                'token' => '',
                'data' => []
            ], 200);
        }

        User::where('id', $request->user_id)
            ->update([
                'password' => Hash::make($request->password),
                'updated_at' => Carbon::now()
            ]);

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
        ], 200);

    }

    public function getrole(Request $request) {
        $role = User::where('id', $request->id)->pluck('user_type')[0];
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $role
        ]);
    }

    public function loginuser(Request $request) {
        $credentials = request(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['msg' => 'Unauthorized', 'status'=>0], 200);
        }

        $user = Auth::user();

        if ($user->is_verified == 1) {
            return response()->json([
                'msg' => 'authenticated',
                'status' => 1,
                'token' => $token,
                'data' => $user
            ]);
        }
        return response()->json([
            'msg' => 'unverified',
            'status' => -1,
            'token' => '',
            'data' => ''
        ]);

    }

    public function verifyemail(Request $request) {
        $user = User::where('access_code', '=', $request->verif)->where('is_verified', 0)->first();
        if ($user) {
            User::where('id', $user->id)->update([
                'access_code' => null,
                'is_verified' => 1,
                'email_verified_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return response()->json([
                'msg' => 'verified',
                'status' => 1,
                'token' => Auth::login($user),
                'data' => $user
            ]);
        }
        Coins::insert($user->id, 0, 0, Config('constant.CONSTANT_NEW_ACCOUNT_COINS'), 'new account', 'credit', 'account');
        return response()->json([
            'msg' => 'invalid_verif',
            'status' => 0
        ]);
    }

    public function registeruser(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'msg' => $validator->errors()->all(),
                'status' => 0,
                'token' => '',
                'data' => []
            ], 200);
        }
        $explode_name = explode(' ', $request->name);
        $slug_raw = '';
        if (array_key_exists(1, $explode_name)) { //check if name value is single string or string separated by space
            $slug_raw = implode('-', array(strtolower($explode_name[0]),strtolower($explode_name[1]),time()));
        } else {
            $slug_raw = implode('-', array(strtolower($explode_name[0]),time()));
        }

        $slug = preg_replace('/\s+/', '', $slug_raw);

        $verif_link =  base64_encode(time() . $explode_name[0] . $slug . Hash::make($request->password));

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_verified = 0;
        $user->slug = $slug;
        $user->user_type = 'student';
        $user->access_code = $verif_link;
        $user->created_at = Carbon::now();
        $user->updated_at = Carbon::now();
        $user->save();

        RegisterVerification::dispatch($request->name, $request->email, config('customvar.app_url').'/auth/verify/'.$verif_link );

        return response()->json([
            'msg' => 'for_verification',
            'status' => 1,
            'token' => '',
            'data' => []
        ], 200);
    }

    public function registerviaprovider(Request $request) {
        $user = User::where('provider_id', $request->provider_id)->first();

        if ($user == null || !$user) {
            $slug_raw = implode('-', array(strtolower($request->given_name),strtolower($request->family_name),time()));
            $slug = preg_replace('/\s+/', '', $slug_raw);

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->avatar = $request->picture;
            $user->slug = $slug;
            $user->is_verified = 1; //If user registers via socialite, then user's email is automatically verified
            //$user->access_code = md5(time() . $data->user['given_name'] . $slug);
            $user->user_type = 'student';
            $user->provider_id = $request->provider_id;
            $user->save();

            $token = Auth::login($user);

            return response()->json([
                'status' => 1,
                'token' => $token,
                'data' => $user
            ], 200);
        } else {

            return response()->json([
                'status' => 0,
                'token' => '',
                'data' => '[]'
            ], 200);
        }
    }

    public function loginviaprovider(Request $request) {
        $user = User::where('provider_id', $request->provider_id)->first();
        if ($user) {
            try {
                // verify the credentials and create a token for the user


                if (! $token = JWTAuth::fromUser($user)) {
                    return response()->json(['error' => 'user not found'], 401);
                }
            } catch (JWTException $e) {
                // something went wrong
                return response()->json(['error' => $e->message], 500);
            }
            // if no errors are encountered we can return a JWT
            return response()->json([
                'status' => 1,
                'token' => compact('token'),
                'data' => $user
            ], 200);
        }

        return response()->json([
            'status' => 0,
            'token' => '',
            'data' => []
        ], 200);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }





    /**
     * SOCIALITE GOOGLE
     */
    public function redirectToGoogle()
    {
        return Socialite::with('google')->stateless()->redirect()->getTargetUrl();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            dd($user);

            //return $this->_registerOrLoginUser($user);

            //$this->_registerOrLoginUser($user);

            //return redirect()->route('feed', [$this->to_pass_user_id]);
            //return redirect()->route('feed');
            //return redirect('feed')->with('user_id', $this->to_pass_user_id);

        } catch (Exception $e) {

            dd($e->getMessage());

        }
    }

    public function _registerOrLoginUser($data)
    {
        $user = User::where('email', '=', $data->email)->first();

        return $user;

        /*if(!$user) { //If user not exist, create

            try {
                $slug_raw = implode('-', array(strtolower($data->user['given_name']),strtolower($data->user['family_name']),time()));
                $slug = preg_replace('/\s+/', '', $slug_raw);

                $user = new User();
                $user->name = $data->name;
                $user->email = $data->email;
                $user->avatar = $data->avatar;
                $user->slug = $slug;
                $user->is_verified = 1; //If user registers via socialite, then user's email is automatically verified
                $user->access_code = md5(time() . $data->user['given_name'] . $slug);
                $user->provider_id = $data->id;
                $user->save();

                $token = Auth::login($user);

            } catch (Exception $e) {

                dd($e->getMessage()+" registerorlogin ");

            }

        } else { //If exist, login

            User::where('id', $user->id)
                ->where('email', $user->email)
                ->update(['access_code' => md5(time() . $user->name . $user->slug)]);
            $token = Auth::login($user);

        }*/
    }










    public function invalidateaccesscode(Request $request) {
        User::where('id', $request->user_id)
            ->update(['access_code' => null]);
    }

    public function apigetuser() {
        $data = [auth()->user()];

        return response()->json($data);
    }

    public function apiautologin(Request $request) {
        $access_code = $request->access_code;


        $user = User::where('access_code', $access_code)->first();
        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'user not found'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }
}
