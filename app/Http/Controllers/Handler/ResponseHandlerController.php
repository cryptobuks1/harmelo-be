<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseHandlerController extends Controller
{
    public function failResponse($data) {
        return response()->json([
            'msg' => 'bad',
            'status' => 0,
            'data' => $data
        ]);
    }

    public function successResponse($data) {
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $data
        ]);
    }
}
