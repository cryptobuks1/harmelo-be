<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile() {
        return view('modules.profile.profile');
    }
    public function profilesettings() {
        return view('modules.profile.shared.profile-settings');
    }

    public function getuserbyslug(Request $request) {

    }
}
