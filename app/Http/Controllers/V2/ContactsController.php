<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Contacts;
use App\Models\EmailLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['get']]);
    }
    public function get(Request $request) {
        $list = Contacts::where('user_id', $request->user_id)->where('is_delete', 0)->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function getProfileByClientID(Request $request) {
        $list = Contacts::where('user_id', $request->user_id)->where('client_id', $request->client_id)->where('is_delete', 0)->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function getEmailLogs(Request $request) {
        $list = EmailLogs::where('user_id', $request->user_id)->where('recipient_id', $request->id)->get();
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => $list
        ]);
    }
    public function sendEmail(Request $request) {
        $subject = $request->subject;
        $email = $request->email;
        $name = $request->name;
        $company_name = User::getName($request->id);

        $result = [];
        $result = Mail::send('emails.email',
            [
                'content'=>  $request->content,
            ],
            function($message) use($email, $name, $subject, $company_name) {
                $message->to($email, $name)->subject($subject);
                $message->from('elbert.softwaredev@gmail.com','Harmelo Music | '. $company_name);
        });


        EmailLogs::insert($request->user_id, '', $request, $request->title, $request->source, 'queued');
        return response()->json([
            'msg' => 'ok',
            'status' => 1,
            'data' => []
        ]);
    }
}
