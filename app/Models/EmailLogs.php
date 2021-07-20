<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLogs extends Model
{
    use HasFactory;

    protected $table = 'tbl_email_logs';

    public static function insert($user_id, $message_id, $request, $title, $source, $event) {
        $message_id = trim($message_id, "<>");
        $e = new EmailLogs();
        $e->message_id = $message_id;
        $e->user_id = $user_id;    
        $e->recipient_id = $request->id;
        $e->name = $request->name;
        $e->email = $request->email;
        $e->subject = $request->subject;
        $e->body = $request->content;
        $e->title = $title;
        $e->source = $source;
        $e->event = $event;
        $e->save();       

    }
    public static function insertV2($user_id, $recipient_id, $name, $email, $subject, $body, $title, $source, $event) {

        $e = new EmailLogs();
        $e->user_id = $user_id;    
        $e->recipient_id = $recipient_id;
        $e->name = $name;
        $e->email = $email;
        $e->subject = $subject;
        $e->body = $body;
        $e->title = $title;
        $e->source = $source;
        $e->event = $event;
        $e->save();       

    }
}
