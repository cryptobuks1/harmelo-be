<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAuthControllerV2;
use App\Http\Controllers\Api\ApiInstrumentCategoryController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\InstrumentCategoryController;
use App\Http\Controllers\Api\LessonBookingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\InstrumentCategoryController as ControllersInstrumentCategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\V2\paymongo\CardController;
use App\Http\Controllers\V2\paymongo\GCashController;
use App\Http\Controllers\V2\paymongo\internal\InternalController;
use App\Http\Controllers\V2\PayMongoController;
use App\Http\Controllers\V2\UserPointsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api', 'cors'])->group(function ($router) { //'cors'

    /**
     * LoginController
     */
    Route::post('providersignin', [AuthController::class, 'providersignin']); //not used
    Route::post('apigetuser', [ApiAuthControllerV2::class, 'apigetuser']);

    /**
     * AuthController
     */
    Route::post('test', [AuthController::class, 'test']); //not used

    /**
     * Api/ApiAuthControllerV2
     */
    Route::post('apiautologin', [ApiAuthControllerV2::class, 'apiautologin']);
    Route::post('invalidateaccesscode', [ApiAuthControllerV2::class, 'invalidateaccesscode']);
    //Socialite Google
    Route::post('redirectToGoogle', [ApiAuthControllerV2::class, 'redirectToGoogle']);
    Route::post('handleGoogleCallback', [ApiAuthControllerV2::class, 'handleGoogleCallback']);
    Route::post('_registerOrLoginUser', [ApiAuthControllerV2::class, '_registerOrLoginUser']);
    Route::post('loginviaprovider', [ApiAuthControllerV2::class, 'loginviaprovider']);

    /**
     * ApiUserController
     */
    Route::post('getallteacher', [ApiUserController::class, 'getallteacher']);
    Route::post('usergetuserdetailsbyslug', [ApiUserController::class, 'usergetuserdetailsbyslug']);
    Route::post('submitusercv', [ApiUserController::class, 'submitusercv']);
    Route::post('editusercv', [ApiUserController::class, 'editusercv']);
    Route::post('submitcoachingapplication', [ApiUserController::class, 'submitcoachingapplication']);
    Route::post('isusercoachingapplied', [ApiUserController::class, 'isusercoachingapplied']);
    Route::post('changeapptheme', [ApiUserController::class, 'changeapptheme']);
    Route::post('getpendingapplications', [ApiUserController::class, 'getpendingapplications']);
    Route::post('getuserinstrumentsandrates', [ApiUserController::class, 'getuserinstrumentsandrates']);

    /**
     * LessonBookingController
     */
    Route::post('createschedule', [LessonBookingController::class, 'createschedule']);
    Route::post('getinstructorschedule', [LessonBookingController::class, 'getinstructorschedule']);
    Route::post('updatebookingstatus', [LessonBookingController::class, 'updatebookingstatus']);
    Route::post('getuserbookingschedule', [LessonBookingController::class, 'getuserbookingschedule']);

    /**
     * Api/InstrumentCategoryController
     */
    Route::post('instrumentcategorygetallinstruments', [ApiInstrumentCategoryController::class, 'instrumentcategorygetallinstruments']);
    Route::post('instrumentgetallinstruments', [ApiInstrumentCategoryController::class, 'instrumentgetallinstruments']);

    /**
     *
     *
     * V2 APIS
     *
     *
     *
     */

     /**
      * GCashController 
      */
      Route::post('creategcashsource', [GCashController::class, 'creategcashsource']);
      Route::post('retrievegcashsource', [GCashController::class, 'retrievegcashsource']);
      Route::post('creategcashpayment', [GCashController::class, 'creategcashpayment']);

      /**
      * InternalController  
      */  
      Route::post('insertbanktransaction', [InternalController::class, 'insertbanktransaction']);
      Route::post('updateuserbanktransactionstatus', [InternalController::class, 'updateuserbanktransactionstatus']);
      Route::post('insertorupdateuserpoints', [InternalController::class, 'insertorupdateuserpoints']);
      Route::get('getusertransactionall', [InternalController::class, 'getusertransactionall']);

      /**
      * UserPointsController 
      */
      Route::post('getuserpoints', [UserPointsController::class, 'getuserpoints']);

      /**
       * CardController   
       */
      Route::post('createpaymentintent', [CardController::class, 'createpaymentintent']);
      Route::post('createpaymentmethod', [CardController::class, 'createpaymentmethod']);
      Route::post('attachtopaymentintent', [CardController::class, 'attachtopaymentintent']);
      Route::post('retrievepaymentintent', [CardController::class, 'retrievepaymentintent']);

         
//    Route::group(['prefix' => 'booking'], function () {

     Route::post('add', [BookingController::class, 'add']);

//    });
    
});

//NOT TOKEN AUTHENTICATED APIs
/**
 * Api/ApiAuthController
 */
Route::post('registernative', [ApiAuthController::class, 'registernative']);
Route::post('getuserbyaccesscode', [ApiAuthController::class, 'getuserbyaccesscode']);

/**
 * Api/ApiAuthControllerV2
 */

