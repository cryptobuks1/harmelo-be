<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\ProfileVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        /**
         * Exclude functions from Token authentication -> except => ['function1', 'function2', ...]
         */
        $this->middleware('auth:api', ['except' => ['userbyslugunauth']]);
    }

    public function edituserslug(Request $request) {
        $update = User::where('id', $request->user_id)
            ->update([
                'slug' => $request->slug
            ]);
        if ($update) {
            return response()->json([
                'msg' => 'ok',
                'status' => 1,
                'data' => User::where('id', $request->user_id)->first()
            ], 200);
        }
    }

    public function getprofilevisits(Request $request) {
        $visits = ProfileVisit::where('user_id', $request->user_id)->where('page', 'profile')->get();
        return response()->json([
            'msg'=>'ok',
            'status'=>1,
            'data'=>$visits
        ]);
    }

    public function visitprofile(Request $request) {
        $has_visited = ProfileVisit::where('visitor_id', $request->visitor_id)
            ->where('user_id', $request->user_id)->where('page', 'profile')->first();

        if (!$has_visited) {
            $insert = new ProfileVisit();
            $insert->user_id = $request->user_id;
            $insert->visitor_id = $request->visitor_id;
            $insert->page = 'profile';
            $insert->created_at = Carbon::now();
            $insert->updated_at = Carbon::now();
            $insert->save();

            return response()->json([
                'msg'=>'ok',
                'status'=>1,
                'data'=>[]
            ]);
        }
    }

    public function editinfo(Request $request) {
        if ($request->image != 'none') {
            $request->validate([
                'image' => 'mimes:png,jpg,jpeg|max:20000'
            ]);
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = $fileName.base64_encode(time().$fileName).'.'.$file->getClientOriginalExtension();

                $update = User::where('id', $request->user_id)
                    ->update([
                        'name'=>$request->name,
                        'email_alt'=>$request->email,
                        'contact_no'=>$request->contact_no,
                        'website'=>$request->website,
                        'profession'=>$request->profession,
                        'avatar'=>$fileName
                    ]);
                if ($update) {
                    if (Storage::disk('s3')->put('public/user/'.$request->user_id.'/avatar/'.$fileName, file_get_contents($file), 'public')) {
                        return response()->json([
                            'msg' => 'ok',
                            'status' => 1,
                            'data' => User::where('id', $request->user_id)->first()
                        ], 200);
                    } else {
                        return response()->json([
                            'msg' => 'storage_put_failed',
                            'status' => -1,
                            'data' => []
                        ], 200);
                    }
                }
                //else
                return response()->json([
                    'msg' => 'bad',
                    'status' => 0,
                    'data' => []
                ], 200);
            }
        } else {
            $update = User::where('id', $request->user_id)
                ->update([
                    'name'=>$request->name,
                    'email_alt'=>$request->email,
                    'contact_no'=>$request->contact_no,
                    'website'=>$request->website,
                    'profession'=>$request->profession
                ]);
            if ($update) {
                return response()->json([
                    'msg' => 'ok',
                    'status' => 1,
                    'data' => User::where('id', $request->user_id)->first()
                ], 200);
            }
        }
    }

    public function userbyslug(Request $request) {
        $user = User::getUserDetailsBySlug($request->slug);
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $user
        ]);
    }

    public function userbyslugunauth(Request $request) {
        $user = User::getUserDetailsBySlug($request->slug);
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $user
        ]);
    }

    public function editcontact(Request $request) {
        $update = User::where('id', '=', $request->user_id)
            ->update([
                'email' => $request->email,
                'contact_no', $request->contact_no,
                'website' => $request->website
            ]);

        if (!$update) {
            return response()->json([
                'msg' => 'bad',
                'status' => 0,
                'data' => []
            ]);
        }

        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => User::where('user_id', $request->user_id)
        ]);
    }
}
