<?php

namespace App\Models;

use App\Services\Utils;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class Appointments extends Model
{
    use HasFactory;
    protected $table = 'tbl_appointments';
    protected $appends = ['start', 'end', 'color', 'timed', 'name'];

    public static function book($user_id, $request, $event) {

        $instructor_name = User::getName($user_id);
        $book = new Appointments();
        $book->user_id = $user_id;
        $book->client_id = $request->client_id;
        $book->client_name = $request->client_name;
        $book->event_id = $request->event_id;
        $book->event_date = $event->event_date;
        $book->time_start =  strtoupper($request->time_start);
        $book->time_end =   strtoupper($request->time_end);
        $book->event_desc =  $event->descriptions;
        $book->event_name = $event->name;
        $book->url =  $event->links;
        $book->price = $event->price;
        $book->event_type = 'appt';
        $book->save();
        $insert_id =  $book->id;
        $email  = $request->email;
        $name  = $request->client_name;

        $exists = Contacts::where('user_id', $user_id)->where('email', $request->email)->first();
        if (empty($exists)) {
            Contacts::add($user_id, $request->client_id, $request->email, $request->phone, $request->client_name, $request->avatar);
        }
        Mail::send('emails.booking-email',
        [
                'receiver'=>  $request->client_name,
                'set_date'=> $event->event_date,
                'time_start' => $request->time_start,
                'time_end' =>  $request->time_end,
                'meeting_link'=> $event->links,
                'name'=> $event->name,
                'desc'=> $event->descriptions,
                'instructor_name'=> $instructor_name,
                'instrument_name'=> $event->instrument_names
            ],
            function($message) use($email, $name) {
                $message->to($email, $name)->subject('You have successfully booked a lesson session!');
                $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });

        return $insert_id;
    }
    public static function bookClass($user_id, $request, $event) {
        /*
        $path = 'call?q='. $event->slug_url.'&id='.$event->id;
        AutoLogin::insert($request->client_id, md5(time(). '  '.''.$user_id), $path, 'booking');
        */

        $code = Utils::generateCode($request->name, $user_id);
        $auto_login_link = Utils::getTinyUrl('https://api.harmelo.com/auto-login/?q='.Str::of($event->name)->slug('-').'&cid='.$request->client_id.'&id='.$request->event_id.'&code='.$code);

        AutoLogin::insert($user_id, $code, $auto_login_link, 'Events');


        $instructor_name = User::getName($user_id);
        $book = new Appointments();
        $book->user_id = $user_id;
        $book->client_id = $request->client_id;
        $book->client_name = $request->client_name;
        $book->event_id = $request->event_id;
        $book->event_date = $event->event_date;
        $book->time_start =  $event->time_start;
        $book->time_end =  $event->time_end;
        $book->event_desc =  $event->descriptions;
        $book->event_name = $event->name;
        $book->url =  $auto_login_link;
        $book->price = $event->price;
        $book->event_type = $event->event_type;
        $book->save();
        $insert_id =  $book->id;
        $email  = $request->email;
        $name  = $request->client_name;

        $exists = Contacts::where('user_id', $user_id)->where('email', $request->email)->first();
        if (empty($exists)) {
            Contacts::add($user_id, $request->client_id, $request->email, $request->phone, $request->client_name, $request->avatar);
        }


        //Send mail to enrollee
        Mail::send('emails.booking-email',
            [
                'receiver'=>  $request->client_name,
                'set_date'=> $event->event_date,
                'time_start' => $event->time_start,
                'time_end' =>  $event->time_end,
                'meeting_link'=> $event->links,
                'name'=> $event->name,
                'desc'=> $event->descriptions,
                'instructor_name'=> $instructor_name,
                'instrument_name'=> $event->instrument_names
            ],
            function($message) use($email, $name) {
                $message->to($email, $name)->subject('You have successfully booked a lesson session!');
                $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });

      //  $current_points = UserPoints::getCurrentPoints($request->client_id);
       // $points = $current_points - $event->price;
      //  UserPoints::where('user_id', $request->client_id)->update(['points' => $points]);

        return $insert_id;

    }
    public static function getAppointmentStatus($client_id, $event_id) {
        $result = Appointments::where('client_id', $client_id)->where('event_id', $event_id)->where('status', '!=', -1)->get();
        if (count($result) > 0)
            return 1;
        return 0;
    }
    public static function getLostsAvailable($event_id, $spaces) {
        $count = Appointments::where('event_id', $event_id)->count();
        return $spaces -  $count;
    }
    public static function sendApprovedEmail($request) {
        $appt = Appointments::where('id', $request->id)->first();
        $event_id = $appt->event_id;
        $event = Events::where('id', $event_id)->first();
        $instrunctor = User::where('id', $appt->user_id)->first();
        $client = User::where('id', $appt->client_id)->first();
        $contacts =  Contacts::where('user_id', $appt->user_id)->where('client_id', $appt->client_id)->where('is_delete', 0)->first();

        $email = $client->email;
        $name = $client->name;


        Mail::send('emails.bookings-approved',
        [
            'receiver'=>  $appt->client_name,
            'set_date'=> $appt->event_date,
            'time_start' => $appt->time_start,
            'time_end' =>  $appt->time_end,
            'meeting_link'=> $event->links,
            'name'=> $appt->event_name,
            'desc'=> $appt->event_desc,
            'instructor_name'=> $instrunctor->name,
            'instrument_name'=> $event->instrument_names
        ],
        function($message) use($email, $name) {
            $message->to($email, $name)->subject('Booking was approved');
            $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });
        EmailLogs::insertV2($request->user_id, $contacts->id,   $appt->client_name, $client->email,  'Booking was approved', '', 'Booking Approved', 'Bookings', 'queued');

        $m = $appt->client_name . ' booking request was successfully approved.';
        ActivityLogs::insert($request->user_id, $appt->client_id, $request->id, '', 'Booking Approved', 'ti-check', 'Bookings', 'bookings-approved', $m);
    }

    public static function sendReminderEmail($request) {
        $appt = Appointments::where('id', $request->id)->first();
        $event_id = $appt->event_id;
        $event = Events::where('id', $event_id)->first();
        $instrunctor = User::where('id', $appt->user_id)->first();
        $client = User::where('id', $appt->client_id)->first();
        $contacts =  Contacts::where('user_id', $appt->user_id)->where('client_id', $appt->client_id)->where('is_delete', 0)->first();

        $email = $client->email;
        $name = $client->name;

        Mail::send('emails.bookings-reminder',
        [
            'receiver'=>  $appt->client_name,
            'set_date'=> $appt->event_date,
            'time_start' => $appt->time_start,
            'time_end' =>  $appt->time_end,
            'meeting_link'=> $event->links,
            'name'=> $appt->event_name,
            'desc'=> $appt->event_desc,
            'instructor_name'=> $instrunctor->name,
            'instrument_name'=> $event->instrument_names
        ],
        function($message) use($email, $name) {
            $message->to($email, $name)->subject('Booking reminder');
            $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });
        EmailLogs::insertV2($request->user_id, $contacts->id,   $appt->client_name, $client->email,  'Booking reminder', '', 'Booking reminder', 'Bookings', 'queued');

        $m = $appt->client_name . ' booking request was dis-approved.';
        ActivityLogs::insert($request->user_id, $appt->client_id, $request->id, '', 'Booking reminder', 'ti-close', 'Bookings', 'bookings-reminder', $m);
    }
    public static function sendDisApprovedEmail($request) {
        $appt = Appointments::where('id', $request->id)->first();
        $event_id = $appt->event_id;
        $event = Events::where('id', $event_id)->first();
        $instrunctor = User::where('id', $appt->user_id)->first();
        $client = User::where('id', $appt->client_id)->first();
        $contacts =  Contacts::where('user_id', $appt->user_id)->where('client_id', $appt->client_id)->where('is_delete', 0)->first();

        $email = $client->email;
        $name = $client->name;


        Mail::send('emails.bookings-dis-approved',
        [
            'receiver'=>  $appt->client_name,
            'set_date'=> $appt->event_date,
            'time_start' => $appt->time_start,
            'time_end' =>  $appt->time_end,
            'meeting_link'=> $event->links,
            'name'=> $appt->event_name,
            'desc'=> $appt->event_desc,
            'instructor_name'=> $instrunctor->name,
            'instrument_name'=> $event->instrument_names
        ],
        function($message) use($email, $name) {
            $message->to($email, $name)->subject('Booking was dis-approved');
            $message->from('elbert.softwaredev@gmail.com','Harmelo MusicED');
        });
        EmailLogs::insertV2($request->user_id, $contacts->id,   $appt->client_name, $client->email,  'Booking was dis-aapproved', '', 'Booking Dis-Approved', 'Bookings', 'queued');

        $m = $appt->client_name . ' booking request was dis-approved.';
        ActivityLogs::insert($request->user_id, $appt->client_id, $request->id, '', 'Booking Dis-Approved', 'ti-close', 'Bookings', 'bookings-dis-approved', $m);
    }

    public static function reconcileLogs($request) {
        $appt = Appointments::where('id', $request->id)->first();

        $m = $appt->client_name . ' that was booked to an event of ' . $appt->event_name . ' has been reconciled';
        ActivityLogs::insert($request->user_id, $appt->client_id, $request->id, '', 'Booking Reconciled', 'ti-check', 'Bookings', $request->attendance, $m);
    }

    public function getStartAttribute() {
        return  (SupportCarbon::parse($this->event_date . 'T' . date("H:i:s", strtotime($this->time_start)))->timestamp) * 1000;

     }
    public function getEndAttribute() {
        return (Carbon::parse($this->event_date .  'T' . date("H:i:s", strtotime($this->time_end)))->timestamp) * 1000;
    }
    public function getNameAttribute() {
        return $this->event_name;
    }
    public function getColorAttribute() {
        if ($this->event_type == 'appt')
            return 'green';
        else return '#2196f3';
    }
    public function getTimedAttribute() {
        return true;
    }
    public function events()
    {
        return $this->belongsTo(Events::class, 'event_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }
    public function notes()
    {
        return $this->hasMany('App\Models\EventsNotes', 'event_id', 'event_id');
    }
}
