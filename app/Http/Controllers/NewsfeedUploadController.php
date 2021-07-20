<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsfeedUploadController extends Controller
{
    public function feedupload() {
        return view('modules.newsfeed.shared.newsfeed-upload');


    }
}
