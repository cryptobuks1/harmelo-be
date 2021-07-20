<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//Passport
use Laravel\Passport\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    //First ci/cd 
    //Second try
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['value', 'classcount', 'apptcount'];

    public static function getUserDetailsBySlug($slug) {
        return User::where('slug', $slug)->get();
    }

    public static function getUserDetailsByID($user_id) {
        return User::where('id', $user_id)->get();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider_id' //added for 3rd party login
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getAuthPassword()
    {
        return $this->password;
    }
    public static function getUserFriends($user_id) {
        return User::where('id', $user_id)->get();
    }

    public static function getUserIDBySlug($slug) {
        return User::where('slug', $slug)->pluck('id');
    }
    public static function getName($id) {
        $name = '';
        $result = User::where('id', $id)->first(); 
        if ($result)
            return $result->name;
        return $name;
    }
    public function getValueAttribute() {
        return  $this->name;
       //return  date("H:i", strtotime($this->time_start));
    }

    public function getClasscountAttribute() {
        $cnt = Events::where('user_id', '=', $this->id)
            ->where('event_type', '=', 'class')
            ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
            ->count();

        return $cnt;
    }
    public function getApptcountAttribute() {
        $cnt = Events::where('user_id', '=', $this->id)
        ->where('event_type', '=', 'appt')
        ->whereRaw('CONCAT(event_date, " ", time_start) >= now()')
        ->count();

        return $cnt;
    }
}
