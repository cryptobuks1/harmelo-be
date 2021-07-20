<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiMediaController extends Controller
{
    /**
     *
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);

        //$this->middleware('cors');
    }

    public function preuploadmedia(Request $request) {
        $userID = $request->user_id;
        $module = $request->module;
        $file_attachments = $request->file_attachments != 'none' ? json_decode($request->file_attachments) : null;

        if ($file_attachments != null) {
            $file_name = $file_attachments[0]->file_name;
            $file_ext = $file_attachments[0]->file_ext;
            Storage::disk('s3')->put('public/media/'. $userID .'/'. $file_name, base64_decode($file_attachments[0]->file), 'public');


            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ], 200);
            // upon selection of image/video files
            // store the file object into 2 variables, var1 = array and  var2 = temporary array variables
            // after selection -> pre-upload all file objects of var1 and clear the var1 content
            // so that when user selects new file, the new selected file does not stack with the old one
            // finally, when user decides clicks "Upload/Save changes" button
            // Insert all the file objects's filename and extension to the tbl_media;
        }
    }

    public function insertmediadetails(Request $request) {

    }
}
