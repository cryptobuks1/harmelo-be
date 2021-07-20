<?php

namespace App\Http\Controllers;

use App\Models\InstrumentCategory;
use Illuminate\Http\Request;

class InstrumentCategoryController extends Controller
{
    public static function instrumentcategorygetallinstruments() {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => InstrumentCategory::with('instruments')->get()
        ]);
    }

}
