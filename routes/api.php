<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAuthControllerV2;
use App\Http\Controllers\Api\ApiInstrumentCategoryController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\InstrumentCategoryController;
use App\Http\Controllers\Api\LessonBookingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\InstrumentCategoryController as ControllersInstrumentCategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\V2\AdminController;
use App\Http\Controllers\V2\ApplicationController;
use App\Http\Controllers\V2\AppointmentController;
use App\Http\Controllers\V2\BookingController;
use App\Http\Controllers\V2\ContactsController;
use App\Http\Controllers\V2\DashboardController;
use App\Http\Controllers\V2\EventsController;
use App\Http\Controllers\V2\GalleryController;
use App\Http\Controllers\V2\ListingController;
use App\Http\Controllers\V2\PaymentsController;
use App\Http\Controllers\V2\paymongo\CardController;
use App\Http\Controllers\V2\paymongo\GCashController;
use App\Http\Controllers\V2\paymongo\internal\InternalController;
use App\Http\Controllers\V2\PayMongoController;
use App\Http\Controllers\V2\TeacherController;
use App\Http\Controllers\V2\UserController;
use App\Http\Controllers\V2\UserPointsController;
use App\Http\Controllers\V2\UserReviewController;
use App\Http\Controllers\V2\WorkExperienceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//
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

Route::group([
   'middleware' => 'api'
], function ($router) { //'cors'

    /**
     * LoginController
     */
    Route::get('logout', [AuthController::class, 'logout']); //not used


    Route::post('providersignin', [AuthController::class, 'providersignin']); //not used
    Route::post('apigetuser', [ApiAuthControllerV2::class, 'apigetuser']);

    /**
     * AuthController
     */
    Route::get('test', [AuthController::class, 'test']); //not used

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
    Route::post('registerviaprovider', [ApiAuthControllerV2::class, 'registerviaprovider']);
    Route::post('registeruser', [ApiAuthControllerV2::class, 'registeruser']);
    Route::post('verifyemail', [ApiAuthControllerV2::class, 'verifyemail']);
    Route::post('loginuser', [ApiAuthControllerV2::class, 'loginuser']);
    Route::post('getrole', [ApiAuthControllerV2::class, 'getrole']);

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
      Route::post('cancelpayment', [GCashController::class, 'cancelpayment']);

      /**
      * InternalController
      */
      Route::post('insertbanktransaction', [InternalController::class, 'insertbanktransaction']);
      Route::post('updateuserbanktransactionstatus', [InternalController::class, 'updateuserbanktransactionstatus']);
      Route::post('insertorupdateuserpoints', [InternalController::class, 'insertorupdateuserpoints']);
      Route::get('getusertransactionall', [InternalController::class, 'getusertransactionall']);
      Route::post('updatecardpaymentstatus', [InternalController::class, 'updatecardpaymentstatus']);

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

      /**
       * ApplicationController
       */
      Route::post('submitresume', [ApplicationController::class, 'submitresume']);
      Route::post('approveapplication', [ApplicationController::class, 'approveapplication']);

      /**
       * ListingController
       */
      Route::post('teacherlist', [ListingController::class, 'teacherlist']);

    Route::group(['prefix' => 'listing'], function () {
        Route::post('getpendingapplication', [ListingController::class, 'getpendingapplicationlist']);
    });

    Route::group(['prefix' => 'user'], function () {
      Route::post('userbyslug', [UserController::class, 'userbyslug']);
      Route::post('userbyslugunauth', [UserController::class, 'userbyslugunauth']);
      Route::post('editinfo', [UserController::class, 'editinfo']);
      Route::post('edituserslug', [UserController::class, 'edituserslug']);
    });

    Route::group(['prefix' => 'events'], function () {
      Route::post('add', [EventsController::class, 'add']);
      Route::post('get', [EventsController::class, 'get']);
      Route::post('getbypastorupcoming', [EventsController::class, 'getEventsByPastOrUpcoming']);


      Route::post('add-class', [EventsController::class, 'addClass']);
      Route::post('geteventbyid', [EventsController::class, 'getEventByID']);
      Route::post('getapppointmentlist', [EventsController::class, 'getApppointmentList']);
      Route::post('getclass', [EventsController::class, 'getclass']);
      Route::post('getrecurringclass', [EventsController::class, 'getRecurringClass']);




      Route::post('getapppointmentlistbyteacher', [EventsController::class, 'getApppointmentListByTeacher']);
      Route::post('getAppointmentbyteacher', [EventsController::class, 'getAppointmentByTeacher']);
      Route::post('geteventsbyteacher', [EventsController::class, 'getEventsByTeacher']);
      Route::post('delete', [EventsController::class, 'delete']);
      Route::post('published', [EventsController::class, 'published']);
      Route::post('un-published', [EventsController::class, 'unPublished']);



    });
    Route::group(['prefix' => 'bookings'], function () {;
      Route::post('bookclass', [AppointmentController::class, 'bookClass']);
      Route::post('booksappointment', [AppointmentController::class, 'bookAppointments']);
      Route::post('get-event-by-id', [EventsController::class, 'getEventByID']);


    });
    Route::group(['prefix' => 'contacts'], function () {
      Route::post('get', [ContactsController::class, 'get']);
      Route::post('send-email', [ContactsController::class, 'sendEmail']);
      Route::post('get-email-logs', [ContactsController::class, 'getEmailLogs']);
      Route::post('getprofilebyclientid', [ContactsController::class, 'getProfileByClientID']);
    });

    Route::group(['prefix' => 'appointments'], function() {
        Route::post('getappointmentlistbyclient', [AppointmentController::class, 'getAppointmentByClient']);
        Route::post('getappointmentlistbyteacher', [AppointmentController::class, 'getAppointmentByTeacher']);
        Route::post('approvedappointments', [AppointmentController::class, 'approvedAppointments']);
        Route::post('disapprovedappointments', [AppointmentController::class, 'disApprovedAppointments']);
        Route::post('reconcileappointments', [AppointmentController::class, 'reconcileAppointments']);
        Route::post('send-reminder', [AppointmentController::class, 'sendReminder']);


    });

    Route::group(['prefix' => 'gallery'], function () {
      Route::post('insert', [GalleryController::class, 'insert']);
      Route::post('getallvid', [GalleryController::class, 'getvideoall']);
      Route::post('getallimg', [GalleryController::class, 'getimgall']);
      Route::post('getall', [GalleryController::class, 'getall']);
      Route::post('getintro', [GalleryController::class, 'getintro']);
      Route::post('addintro', [GalleryController::class, 'addintro']);
      Route::post('uploadimages', [GalleryController::class, 'uploadimages']);
      Route::post('getimages', [GalleryController::class, 'getimages']);
      Route::post('canceluploadimage', [GalleryController::class, 'canceluploadimage']);
    });

    Route::group(['prefix' => 'review'], function () {
      Route::post('reviewUser', [UserReviewController::class, 'review']);
      Route::post('get', [UserReviewController::class, 'get']);
      Route::get('getpaginate', [UserReviewController::class, 'getpaginated']);
      Route::post('getrating', [UserReviewController::class, 'getuserrating']);

    });

    Route::group(['prefix' => 'instrument'], function () {
        Route::post('getlist', [ListingController::class, 'instrumentlist']);
    });

    Route::group(['prefix' => 'work'], function () {
        Route::post('add', [WorkExperienceController::class, 'add']);
        Route::post('get', [WorkExperienceController::class, 'get']);
    });
    Route::group(['prefix' => 'dashboard'], function () {
      Route::post('get-data', [DashboardController::class, 'getData']);
      Route::post('get-apppoints-list', [DashboardController::class, 'getAppointmentList']);
      Route::post('get-activity-logs-list', [DashboardController::class, 'getActivityLogs']);
    });
    Route::group(['prefix' => 'visits'], function () {
      Route::post('get', [UserController::class, 'getprofilevisits']);
      Route::post('visit', [UserController::class, 'visitprofile']);
    });

    Route::group(['prefix' => 'apply'], function () {
      Route::post('submit-non-member', [ApplicationController::class, 'submitnonmember']);
      Route::post('get', [ApplicationController::class, 'getapplications']);
      Route::post('approvenonmember', [ApplicationController::class, 'approvenonmember']);
    });

    Route::group(['prefix' => 'payment'], function () {
      Route::post('create-source', [PaymentsController::class, 'createSource']);
      Route::post('insert-transaction', [PaymentsController::class, 'insertbanktransaction']);
      Route::post('retrieve-source', [PaymentsController::class, 'retrieveSource']);
      Route::post('create-payment', [PaymentsController::class, 'createPayment']);
      Route::post('cancel-payment', [PaymentsController::class, 'cancelPayment']);
      Route::post('create-payment-intent', [PaymentsController::class, 'createPaymentIntent']);
      Route::post('create-payment-method', [PaymentsController::class, 'createPaymentMethod']);
      Route::post('attach-to-payment-intent', [PaymentsController::class, 'attachToPaymentIntent']);
      Route::post('insert-or-update-points', [PaymentsController::class, 'insertOrUpdatePoints']);
      Route::post('retrieve-payment-intent', [PaymentsController::class, 'retrievepaymentintent']);
      Route::post('update-payment-status', [PaymentsController::class, 'updatePaymentStatus']);
      Route::post('get-selected-event', [PaymentsController::class, 'getEventByIDOnly']);
      Route::post('add-revenue', [PaymentsController::class, 'addRevenue']);
    });

    Route::group(['prefix' => 'withdraw'], function () {
      Route::post('submit-wrequest', [TeacherController::class, 'submitRequest']);
      Route::post('cancel-wrequest', [TeacherController::class, 'cancelRequest']);
      Route::post('get-request-by-user', [TeacherController::class, 'getRequestByUser']);
      Route::post('get-request-by-admin', [TeacherController::class, 'getRequestByAdmin']);
      Route::post('get-unassigned-requests', [AdminController::class, 'getUnassignedRequests']);
      Route::post('process-request', [AdminController::class, 'proccessRequest']);
      Route::post('cancel-request', [AdminController::class, 'cancelRequest']);
      Route::post('complete-request', [AdminController::class, 'completeRequest']);
      Route::post('get-request-by-status', [AdminController::class, 'getRequestByStatus']);
    });

    Route::group(['prefix' => 'verif'], function () {
      Route::post('pre-submission', [TeacherController::class, 'createVerif']);
      Route::post('verify', [TeacherController::class, 'verifyCode']);
      Route::post('re-submit', [TeacherController::class, 'createVerif']);
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::post('resetPassword', [ApiAuthControllerV2::class, 'resetPassword']);
    });
});

//NOT TOKEN AUTHENTICATED APIs
/**
 * Api/ApiAuthController
 */
Route::post('registernative', [ApiAuthController::class, 'registernative']);
Route::get('registernative', [ApiAuthController::class, 'registernative']);

Route::post('getuserbyaccesscode', [ApiAuthController::class, 'getuserbyaccesscode']);

/**
 * Api/ApiAuthControllerV2
 */

