<?php

namespace App\Services;

use ArieTimmerman\Laravel\URLShortener\URLShortener;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Utils
{

    public static function getTinyUrl($url)  {   
        return (string)URLShortener::shorten($url);
    }
    public static function addDays($d, $number_of_days) {
       return date('Y-m-d', strtotime($d. ' + '.$number_of_days . ' days'));
    }
    public static function addWeeks($d, $week) {
        return date('Y-m-d', strtotime($d. ' + '.$week . ' weeks'));
     }
    public static function isWeekend($date) {
        $status = 0;
        $weekDay = date('w', strtotime($date));
        if ($weekDay == 0 || $weekDay == 6)
            $status = 1;

        return $status;
    }
    public static function getDaysBetween($start, $end) {
        $days = (strtotime($end) - strtotime($start)) / (60 * 60 * 24);
        return $days;
    }
    public static function generateCode($prefix, $suffix) {
        return Hash::make(md5(time().$prefix.$suffix.''.rand(1, 99999999)));
    }
    public static function googleStorage($image, $t) {
        $path = '';
        if ($t == 'appt')
            $path = 'public/thumb/1624163847-appt-thumb.jpg';
        else
            $path = 'public/thumb/1624163944-appt-thumb.jpg';
        if ($image  != 'undefined' && $image  != '') {
            $file_name = ''.time().'-appt-thumb.jpg';
            $file = 'public/thumb/'.$file_name;
            $status =  Storage::disk('s3')->put($file,   base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image)), 'public');
            $path = $file;
        }

        return $path;
    }
}
