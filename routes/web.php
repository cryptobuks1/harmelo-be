<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\InstrumentCategoryController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MusicFeedController;
use App\Http\Controllers\NewsfeedUploadController;
use App\Http\Controllers\TesterPageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFollowerController;
use App\Models\InstrumentCategory;
use Illuminate\Mail\Transport\MailgunTransport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('test-url',  [NewsfeedController::class, 'test'])->name('test');
Route::get('auto-login',  [LandingController::class, 'autoLogin'])->name('autoLogin');

Route::middleware(['auth:web'])->group(function ($router) {

    /**
     * Pages
     */
    Route::get('/feed', [NewsfeedController::class, 'feed'])->name('feed');
    Route::get('/upload', [NewsfeedUploadController::class, 'feedupload'])->name('feedupload');
    Route::get('/user/{slug}', [ProfileController::class, 'profile']);
    Route::get('/marketplace', [MarketplaceController::class, 'marketplace'])->name('marketplace');
    Route::get('/user/settings/{slug}', [ProfileController::class, 'profilesettings']);

    /**
     * MusicFeed
     */
    Route::post('/musicfeedinsertpost', [MusicFeedController::class, 'musicfeedinsertpost'])->name('musicfeedinsertpost');
    Route::post('/musicfeedgetpost', [MusicFeedController::class, 'musicfeedgetpost'])->name('musicfeedgetpost');
    Route::post('/musicfeedreacttopost', [MusicFeedController::class, 'musicfeedreacttopost'])->name('musicfeedreacttopost');
    Route::post('/musicfeedupdatereacttopost', [MusicFeedController::class, 'musicfeedupdatereacttopost'])->name('musicfeedupdatereacttopost');
    Route::post('/musicfeedsharepost', [MusicFeedController::class, 'musicfeedsharepost'])->name('musicfeedsharepost');
    Route::post('/musicfeedgetrandomstrangerusers', [MusicFeedController::class, 'musicfeedgetrandomstrangerusers'])->name('musicfeedgetrandomstrangerusers');
    Route::post('/musicfeedgetolderpost', [MusicFeedController::class, 'musicfeedgetolderpost'])->name('musicfeedgetolderpost');
    Route::post('/musicfeedgetpostbyuserid', [MusicFeedController::class, 'musicfeedgetpostbyuserid'])->name('musicfeedgetpostbyuserid');
    Route::post('/musicfeeduploadcover_v2', [MusicFeedController::class, 'musicfeeduploadcover_v2'])->name('musicfeeduploadcover_v2');

    /**
     * User
     */
    Route::post('/usergetuserdetailsbyslug', [UserController::class, 'usergetuserdetailsbyslug'])->name('usergetuserdetailsbyslug');
    Route::post('/usergetuserbyid', [UserController::class, 'usergetuserbyid'])->name('usergetuserbyid');
    Route::post('/usersearchuser', [UserController::class, 'usersearchuser'])->name('usersearchuser');
    Route::post('/userprofileinsertvisitcount', [UserController::class, 'userprofileinsertvisitcount'])->name('userprofileinsertvisitcount');
    Route::post('/userprofilegetvisitcount', [UserController::class, 'userprofilegetvisitcount'])->name('userprofilegetvisitcount');
    Route::post('/userprofileeditprofile', [UserController::class, 'userprofileeditprofile'])->name('userprofileeditprofile');
    Route::post('/useruploadavatar', [UserController::class, 'useruploadavatar'])->name('useruploadavatar');
    Route::post('/useruploadcover', [UserController::class, 'useruploadcover'])->name('useruploadcover');
    Route::post('/useruploadcover_v2', [UserController::class, 'useruploadcover_v2'])->name('useruploadcover_v2');
    Route::post('/useruploadavatar_v2', [UserController::class, 'useruploadavatar_v2'])->name('useruploadavatar_v2');
    Route::post('/usergetreactionscount', [UserController::class, 'usergetreactionscount'])->name('usergetreactionscount');
    Route::post('/changeapptheme', [UserController::class, 'changeapptheme'])->name('changeapptheme');

    /**
     * UserFollower
     */
    Route::post('/userfollowerfollowuser', [UserFollowerController::class, 'userfollowerfollowuser'])->name('userfollowerfollowuser');
    Route::post('/userfollowerunfollowuser', [UserFollowerController::class, 'userfollowerunfollowuser'])->name('userfollowerunfollowuser');
    Route::post('/userfollowerdeclinerequest', [UserFollowerController::class, 'userfollowerdeclinerequest'])->name('userfollowerdeclinerequest');
    Route::post('/userfolloweracceptrequest', [UserFollowerController::class, 'userfolloweracceptrequest'])->name('userfolloweracceptrequest');
    Route::post('/userfollowergeneratenewsuggesteruser', [UserFollowerController::class, 'userfollowergeneratenewsuggesteruser'])->name('userfollowergeneratenewsuggesteruser');
    Route::post('/userfollowergetfriendrequestlist', [UserFollowerController::class, 'userfollowergetfriendrequestlist'])->name('userfollowergetfriendrequestlist');
    Route::post('/userfollowergetuserfollowingcountbyslug', [UserFollowerController::class, 'userfollowergetuserfollowingcountbyslug'])->name('userfollowergetuserfollowingcountbyslug');
    Route::post('/userfollowergetuserfollowercountbyslug', [UserFollowerController::class, 'userfollowergetuserfollowercountbyslug'])->name('userfollowergetuserfollowercountbyslug');
    Route::post('/userfollowercancelrequest', [UserFollowerController::class, 'userfollowercancelrequest'])->name('userfollowercancelrequest');

    /**
     * InstrumentCategory
     */
 

});

Route::get('/under-construction', [MarketplaceController::class, 'marketplace'])->name('under-construction');
Route::get('/testerpage', [TesterPageController::class, 'testerpage'])->name('testerpage');
Route::get('/404notfound', [LandingController::class, 'notfound'])->name('notfound');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', [LandingController::class, 'landing'])->name('landing');
Route::get('/autologin/{access_code}', [LoginController::class, 'webautologin']);

Route::get('login/google', [LoginController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);

/**
 * LoginController
 */
Route::post('webvalidateuser', [LoginController::class, 'me']);

/**
 * ApiAuthController
 */
Route::post('loginnative', [ApiAuthController::class, 'loginnative']);
Route::post('insertnewverificationcode', [ApiAuthController::class, 'insertnewverificationcode']);
Route::post('validateverificationcode', [ApiAuthController::class, 'validateverificationcode']);
Route::post('invalidateaccesscode', [ApiAuthController::class, 'invalidateaccesscode']);

Route::get('google-calendar/connect',  [ApiAuthController::class, 'store']);



/**
 * MAIL ROUTES
 */
Route::get('/sendvalidatoremail', function() {

    $data = [
        'title' => 'Hello Harmelo Student!!!',
        'content' => '<p style="color:red">This app helps you to master all 4 elements.</p>'
    ];

    Mail::send('emails.test', $data, function($message) {
        $message->to('ammdinopol@gmail.com', 'Miko')->subject('Your Harmelo OTP is 779087');
    });

});





