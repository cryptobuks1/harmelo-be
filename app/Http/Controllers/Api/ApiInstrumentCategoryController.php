<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use App\Models\InstrumentCategory;
use Illuminate\Http\Request;

class ApiInstrumentCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }

    public function instrumentgetallinstruments() {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => Instrument::where('is_delete', 0)->get()
        ]);
    }

    public function instrumentcategorygetallinstruments() {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => InstrumentCategory::with('instruments')->get()
        ]);
    }
}
