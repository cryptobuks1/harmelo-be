<?php

namespace App\Http\Controllers;

use App\Models\AutoLogin;
use App\Models\MeetingCode;
use App\Services\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LandingController extends Controller
{
    public function landing() {
        return view('modules.landing.landing');
    }

    public function notfound() {
        return view('modules.404.not-found');
    }
    public function autoLogin(Request $request) {
        $q = $request->q;
        $id = $request->id;
        $code = $request->code;
        $user_id = $request->cid;

        $exists = AutoLogin::where('code', $code)->first();
        if (empty($exists))
            abort(404);
        
        $meeting_code = Utils::generateCode($q.$code, $id.$code);
        
//        $url2 = 'http://127.0.0.1:8800/';
        $url2 = 'https://meet.harmelo.com/';
        $url = $url2.'?q='.$q.'&cid='.$user_id.'&id='.$id.'&code='.$code.'&ref='.$meeting_code;


        MeetingCode::insert($exists->user_id, $id, $meeting_code,  $url, 'Events');
        return redirect()->to($url);
    }
}
