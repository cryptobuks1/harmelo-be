<?php

namespace App\Http\Controllers;

use App\Models\AutoLogin;
use App\Services\Utils;
use ArieTimmerman\Laravel\URLShortener\URLShortener;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class NewsfeedController extends Controller
{
    public function feed() {
        return view('modules.newsfeed.newsfeed');

    }
    public function test() {
        echo (string)URLShortener::shorten("http://www.example.com");
    }
  
}
