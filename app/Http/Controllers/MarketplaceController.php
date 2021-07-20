<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function marketplace() {
        return view('modules.marketplace.marketplace');
    }
}
