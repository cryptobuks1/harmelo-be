<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TesterPageController extends Controller
{
    public function testerpage() {
        return view('modules.testerpage.testerpage');
    }
}
