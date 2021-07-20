<?php

namespace App\Models;

use Abraham\TwitterOAuth\Util;
use App\Services\Utils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class Events extends Model
{
    use HasFactory;
    protected $table = 'tbl_events';
    protected $appends = ['start', 'end', 'color', 'timed'];
    ////
    /*
    public static function add($user_id, $request) {

        $appt_duration = Events::numbersOnly($request->appt_duration);
        $appt_counts = $request->duration / $appt_duration;

        $path = Utils::googleStorage($request->thumb);
        
        $book = new Events();
        $book->user_id = $user_id;
        $book->name = $request->name;
        $book->duration = $request->duration;
        $book->time_start = $request->time_start;
        $book->time_end = $request->time_end;
        $book->event_date = $request->booking_date;
        $book->event_type = $request->event_type;
        $book->instrument_ids = $request->instrument_ids;
        $book->instrument_names = $request->instrument_names;
        $book->client_ids = $request->clients_ids;
        $book->client_names = $request->clients_names;
        $book->descriptions  = $request->descriptions;
        $book->cancellation = $request->cancel_limit;
        $book->cancellation_type = $request->cancel_type;
        $book->price = $request->price;
        $book->slug_url = Str::of($request->name)->slug('-');
        $book->appt_duration = $appt_duration;
        $book->appt_counts = $appt_counts;
        $book->thumb = $path;
        $book->is_published = (int) $request->is_published;
        $book->save();
        $parent_id =  $book->id;

        Events::where('id', $parent_id)->update([
                'parent_id' => $parent_id,
                'links' =>   Utils::getTinyUrl(env('APP_URL').'/call/?q='.Str::of($request->name)->slug('-').'&id='.$parent_id)]);
        $notes = EventsNotes::add($user_id, $parent_id, $request->notes);
        $instruments_list = explode(',', $request->instrument_names);
        for ($i = 0; $i < count($instruments_list); $i++) {
            EventsInstrument::insert($parent_id, $parent_id, $user_id, $instruments_list[$i]);
        }

        if ($request->recurring_type != 'Never') {
            $end = 0;
            if ($request->end_type == 'After')
                $end = $request->end_after_occurence;
            else if ($request->end_type == 'On') {
                $end =  Utils::getDaysBetween( $request->booking_date, $request->end_date);
            }
            if ($request->recurring_session == 1) {
                for ($i = 1; $i <= $end; $i++) {

                    if ($request->recurring_type == 'Daily') {
                        $is_weekend = 0;
                        if ($request->daily_recur_type == 'everyday') {
                            $booking_date = Utils::addDays($request->booking_date, ($request->daily_recur * $i));
                            $is_weekend = 0;
                        } else if ($request->daily_recur_type == 'weekday') {
                            $booking_date = Utils::addDays($request->booking_date, $i);
                            $is_weekend = Utils::isWeekend($booking_date);
                        }
                    } else if ($request->recurring_type == 'Weekly') {
                        $is_weekend = 0;
                        $booking_date = Utils::addWeeks($request->booking_date, ($request->weekly_recur * $i));
                    }
                    if ($is_weekend == 0) {
                       Events::insert($user_id, $parent_id, $booking_date, $request, $appt_duration, $path, $instruments_list, $appt_counts);
                    }
                }
            }
        }
    }
    */
    public static function insert($user_id, $parent_id, $booking_date,  $request, $appt_duration, $path, $instruments_list, $appt_counts) {


        $book = new Events();
        $book->user_id = $user_id;
        $book->name = $request->name;
        $book->duration = $request->duration;
        $book->time_start = $request->time_start;
        $book->time_end = $request->time_end;
        $book->event_date = $booking_date;
        $book->event_type = $request->event_type;
        $book->instrument_ids = $request->instrument_ids;
        $book->instrument_names = $request->instrument_names;
        $book->client_ids = $request->clients_ids;
        $book->client_names = $request->clients_names;
        $book->descriptions  = $request->descriptions;
        $book->cancellation = $request->cancel_limit;
        $book->cancellation_type = $request->cancel_type;
        $book->price = $request->price;
        $book->slug_url = Str::of($request->name)->slug('-');
        $book->appt_duration = $appt_duration;
        $book->thumb = $path;
        $book->is_published = (int) $request->is_published;
        $book->appt_counts = $appt_counts;
        $book->room_name =  Utils::generateCode($request->name.$booking_date, $user_id.$request->descriptions);
        $book->save();
        $insert_id =  $book->id;

        $code = Utils::generateCode($request->name, $parent_id);
        $auto_login_link = Utils::getTinyUrl('https://api.harmelo.com/auto-login/?q='.Str::of($request->name)->slug('-').'&cid='.$user_id.'&id='.$insert_id.'&code='.$code);

        AutoLogin::insert($user_id, $code, $auto_login_link, 'Events');

        Events::where('id', $insert_id)->update([
                'parent_id' => $parent_id,
                'links' =>  $auto_login_link]);

        $notes = EventsNotes::add($user_id, $insert_id, $request->notes);

        $instruments_list = explode(',', $request->instrument_names);
        for ($i = 0; $i < count($instruments_list); $i++) {
            EventsInstrument::insert($insert_id, $parent_id, $user_id, $instruments_list[$i]);
        }
    }
    public static function addClass($user_id, $request) {
        
        $path = Utils::googleStorage($request->thumb, $request->event_type);
        $appt_duration = 0;
        $appt_counts = 0;
        if ($request->event_type == 'appt') {
            $appt_duration = Events::numbersOnly($request->appt_duration);
            $appt_counts = $request->duration / $appt_duration;
        }
        $book = new Events();
        $book->user_id = $user_id;
        $book->name = $request->name;
        $book->duration = $request->duration;
        $book->time_start = $request->time_start;
        $book->time_end = $request->time_end;
        $book->event_date = $request->booking_date;
        $book->event_type = $request->event_type;
        $book->descriptions  = $request->descriptions;
        $book->instrument_ids = $request->instrument_ids;
        $book->instrument_names = $request->instrument_names;
        $book->client_ids = $request->clients_ids;
        $book->client_names = $request->clients_names;
        $book->cancellation = $request->cancel_limit;
        $book->cancellation_type = $request->cancel_type;
        $book->price = $request->price;
        $book->spaces = $request->slots;
        $book->space_unlimited = ($request->slot_type) ? 1 : 0;
        $book->slug_url = Str::of($request->name)->slug('-');
        $book->thumb = $path;
        $book->appt_duration = $appt_duration;
        $book->appt_counts = $appt_counts;
        $book->is_published = (int) $request->is_published;
        $book->room_name =  Utils::generateCode($request->name.$request->booking_date, $user_id.$request->descriptions);
        $book->save();
        $parent_id =  $book->id;


        $code = Utils::generateCode($request->name, $parent_id);
        $auto_login_link = Utils::getTinyUrl('https://api.harmelo.com/auto-login/?q='.Str::of($request->name)->slug('-').'&cid='.$user_id.'&id='.$parent_id.'&code='.$code);

        AutoLogin::insert($user_id, $code, $auto_login_link, 'Events');
        
        Events::where('id', $parent_id)->update([
            'parent_id' => $parent_id,
            'links' => $auto_login_link]);
        $notes = EventsNotes::add($user_id, $parent_id, $request->notes);
        $instruments_list = explode(',', $request->instrument_names);
        for ($i = 0; $i < count($instruments_list); $i++) {
            EventsInstrument::insert($parent_id, $parent_id, $user_id, $instruments_list[$i]);
        }

        if ($request->recurring_type != 'Never') {
            $end = 0;
            if ($request->end_type == 'After')
                $end = $request->end_after_occurence;
            else if ($request->end_type == 'On') {
                $end =  Utils::getDaysBetween( $request->booking_date, $request->end_date);
            }
            if ($request->recurring_session == 1) {
                for ($i = 1; $i <= $end; $i++) {

                    if ($request->recurring_type == 'Daily') {
                        $is_weekend = 0;
                        if ($request->daily_recur_type == 'everyday') {
                            $booking_date = Utils::addDays($request->booking_date, ($request->daily_recur * $i));
                            $is_weekend = 0;
                        } else if ($request->daily_recur_type == 'weekday') {
                            $booking_date = Utils::addDays($request->booking_date, $i);
                            $is_weekend = Utils::isWeekend($booking_date);
                        }
                    } else if ($request->recurring_type == 'Weekly') {
                        $is_weekend = 0;
                        $booking_date = Utils::addWeeks($request->booking_date, ($request->weekly_recur * $i));
                    }
                    if ($is_weekend == 0) {
                    Events::insert($user_id, $parent_id, $booking_date, $request, $appt_duration, $path, $instruments_list, $appt_counts);
                    }
                }
            }
        }
        
        $t = $request->event_type == 'appt' ? 'Appointments' : 'Class';
        $m = $request->name . ' ' . $t. ' was successfully created';
        ActivityLogs::insert($request->user_id, 0, $parent_id, '', $t.' Created', 'ti-check', $t, strtolower($t.'-created'), $m);

    }
    public static function getParentID($event_id) {
        $id = 0;
        $result = Events::where('id', $event_id)->first(); 
        if ($result)
            return $result->parent_id;
        return $id;
    }
    public static function checkIfAvailable($event_id) {
        return Events::where('parent_id', $event_id)->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
                ->where('is_delete', 0)->where('is_published', 1)->count();
    }
    public function getStartAttribute() {
       return  (Carbon::parse($this->event_date . 'T' . date("H:i:s", strtotime($this->time_start)))->timestamp) * 1000;
      //return  date("H:i", strtotime($this->time_start));

    }
    public function getEndAttribute() {
        return (Carbon::parse($this->event_date .  'T' . date("H:i:s", strtotime($this->time_end)))->timestamp) * 1000;
       // return  date("H:i", strtotime($this->time_end));
    }
    public function getColorAttribute() {
        if ($this->is_published == 1) {
        if ($this->event_type == 'appt')
            return '#4caf50';
        else return '#2196f3';
        } else return '#f44336';
    }
    public function getTimedAttribute() {
        return true;
    }
    public  static function numbersOnly($str) {
       return filter_var($str, FILTER_SANITIZE_NUMBER_INT);
    }
    public function notes()
    {
        return $this->hasMany('App\Models\EventsNotes', 'event_id', 'id');
    }
    public function appointments()
    {
        return $this->hasMany('App\Models\Appointments', 'event_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
