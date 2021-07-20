<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['logout', 'test']]);
    }

    public function logout() {

        Auth::logout();

        return redirect()->route('landing');

    }
    public function test() {
        dd (1);
    }
    public function register() {

    }
}
