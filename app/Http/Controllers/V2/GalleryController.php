<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\UserIntro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getimages(Request $request) {
        $gallery = Gallery::where('user_id', $request->user_id)->where('is_delete', 0)->orderBy('id', 'DESC')->get();

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $gallery
        ], 200);
    }

    public function canceluploadimage(Request $request) {
        $refId = Gallery::where('request_ref_id','=', $request->request_ref_id)
        ->whereNotNull('request_ref_id')
        ->where('is_upload_cancelled', 0)
        ->get();
        if ($refId->count() > 0) {
            $up = Gallery::where('request_ref_id', '=', $request->request_ref_id)
                ->whereNotNull('request_ref_id')
                ->where('is_upload_cancelled', 0)
                ->update([
                    'request_ref_id'=>null,
                    'is_upload_cancelled'=>1
                ]);
            if ($up) {
                foreach($refId as $ref) {
                    Storage::disk('s3')->delete('public/user/'.$request->user_id.'/gallery/'.$ref->file_name);
                }
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => []
                ], 200);
            }
        }

    }

    public function uploadimages(Request $request) {
        request()->validate([
            'files' => 'required',
            'files.*' => 'required|mimes:jpg,jpeg,png,bmp,webp|max:100000'
        ]);

        $insert_data = [];

        if ($request->hasFile('files')) {
            foreach($request->file('files') as $file) {
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = $fileName.base64_encode(time().$fileName).'.'.$file->getClientOriginalExtension();
                Gallery::create([
                    'user_id'=>$request->user_id,
                    'file_name'=>$fileName,
                    'request_ref_id'=>$request->request_ref_id
                ]);

                Storage::disk('s3')->put('public/user/'.$request->user_id.'/gallery/'.$fileName, file_get_contents($file), 'public');

                array_push($insert_data, ['user_id'=>$request->user_id, 'file_name'=>$fileName, 'request_ref_id'=>$request->request_ref_id]);
            }
        }
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ], 200);

        /*if (Gallery::insert($insert_data)) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => []
            ], 200);
        }*/
        /*$validator = Validator::make($request->all(), [
            "files"    => "required|array|",
            "files.*"  => "required|mimes:jpg,jpeg,png,bmp|max:100000",
        ]);

        if ($validator->fails()) {
            dd($validator->errors());
        } else {
            dd('success');
        }*/
    }

    public function addintro(Request $request) {
        if ($request->file === 'none') {
            $exist = UserIntro::where('user_id', $request->user_id)->first();
            if ($exist) {
                $update = UserIntro::where('user_id', $request->user_id)
                    ->update([
                        'title' => $request->title,
                        'description' => $request->description,
                        'updated_at' => Carbon::now()
                    ]);
                if ($update) {
                    return response()->json([
                        'msg' => 'ok',
                        'status' => 1,
                        'data' => UserIntro::where('user_id', $request->user_id)->first()
                    ], 200);
                }
            }

            //else, insert
            $intro = new UserIntro();
            $intro->user_id = $request->user_id;
            $intro->title = $request->title;
            $intro->description = $request->description;
            $intro->created_at = Carbon::now();
            $intro->updated_at = Carbon::now();

            if (!$intro->save()) {
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ], 200);
            }
        } else {
            //validate if file is video
            $validator = $request->validate([
                'video' => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm|max:400000'
            ]);

            $file_array = json_decode($request->file);
            $filename = $file_array[0]->file_name;
            $fileext = $file_array[0]->file_ext;
            $filename_unique = $filename.base64_encode(time().$filename).$fileext;

            //check if user already has a video intro
            $exist = UserIntro::where('user_id', $request->user_id)->first();

            //if exists, update current
            if ($exist) {
                $update = UserIntro::where('user_id', $request->user_id)
                    ->update([
                        'filename' => $filename_unique,
                        'title' => $request->title,
                        'description' => $request->description,
                        'updated_at' => Carbon::now()
                    ]);
                if ($update) {
                    if (Storage::disk('s3')->put('public/user/'.$request->user_id.'/intro/'.$filename_unique, file_get_contents($request->video), 'public')) {
                        return response()->json([
                            'msg' => 'ok',
                            'status' => 1,
                            'data' => UserIntro::where('user_id', $request->user_id)->first()
                        ], 200);
                    } else {
                        return response()->json([
                            'msg' => 'storage_put_failed',
                            'status' => -1,
                            'data' => []
                        ], 200);
                    }
                }
            }

            //else, insert
            $intro = new UserIntro();
            $intro->user_id = $request->user_id;
            $intro->filename = $filename_unique;
            $intro->title = $request->title;
            $intro->description = $request->description;
            $intro->created_at = Carbon::now();
            $intro->updated_at = Carbon::now();

            if (!$intro->save()) {
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ], 200);
            }
            if (Storage::disk('s3')->put('public/user/'.$request->user_id.'/intro/'.$filename_unique, file_get_contents($request->video), 'public')) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => $intro
                ], 200);
            } else {
                return response()->json([
                    'msg' => 'storage_put_failed',
                    'status' => -1,
                    'data' => []
                ], 200);
            }
        }
    }

    public function getintro(Request $request) {
        //database rule:
        //does not allow storing multiple video per user
        //1 video : 1 user
        //update only, no insert
        $intro = UserIntro::where('user_id', $request->user_id)->where('is_delete', 0)->first();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $intro
        ], 200);
    }

    public function getall(Request $request) {
        $gal = Gallery::where('user_id','=',$request->user_id)->where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $gal
        ], 200);
    }

    public function getvideoall(Request $request) {
        $gal = Gallery::where('user_id','=',$request->user_id)->where('file_type','=','video')->where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $gal
        ], 200);
    }

    public function getimgall(Request $request) {
        $gal = Gallery::where('user_id','=',$request->user_id)->where('file_type','=','image')->where('is_delete', 0)->orderBy('id', 'DESC')->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $gal
        ], 200);
    }

    public function insert(Request $request) {

        $file_array =json_decode($request->file);
        $filename = $file_array[0]->file_name;
        $fileext = $file_array[0]->file_ext;

        $filename_unique = $filename.base64_encode(time()+$filename).$fileext;

        $gal = new Gallery();
        $gal->user_id = $request->user_id;
        $gal->file_name = $filename_unique;
        $gal->file_type = $request->file_type; //video/img/etc...
        $gal->file_ext = $fileext; //extension type of the file
        $gal->file_title = $request->file_title;
        $gal->file_description = $request->file_description;
        $gal->created_at = Carbon::now();
        $gal->updated_at = Carbon::now();

        if (!$gal->save()) {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ], 200);
        }

        if (Storage::disk('s3')->put('public/user/'.$request->user_id.'/gallery/'.$filename_unique, base64_decode($file_array[0]->file), 'public')) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => $gal
            ], 200);
        } else {
            return response()->json([
                'msg' => 'storage_put_failed',
                'status' => -1,
                'data' => []
            ], 200);
        }


    }
}
