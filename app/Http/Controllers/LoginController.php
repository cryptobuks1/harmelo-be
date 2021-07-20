<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{

    public function __construct()
    {

    }

    public function webautologin(Request $request) {
        $access_code = $request->access_code;

        $user = User::where('access_code', $access_code)->first();

        if ($user) {
            Auth::login($user);
            User::where('id', $user->id)->update(['access_code'=>null]);
        }
        return redirect()->route('feed');
    }

    private $to_pass_user_id;

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();

            $this->_registerOrLoginUser($user);

            return redirect()->route('feed');

        } catch (Exception $e) {

            dd($e->getMessage());

        }
    }

    public function _registerOrLoginUser($data)
    {
        $user = User::where('email', '=', $data->email)->first();

        if(!$user) { //If user not exist, create

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

        }

        $this->to_pass_user_id = $user->id; //Store in a variable instead of directly redirecting to route because it wont work for some reason
    }

    public function me()
    {
        $data = [auth()->user()];

        return response()->json($data);
    }

}
