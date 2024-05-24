<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::namespace('Api')->name('api.')->group(function () {
    Route::get('general-setting', 'BasicController@generalSetting');
    Route::get('unauthenticate', 'BasicController@unauthenticate')->name('unauthenticate');
    Route::get('languages', 'BasicController@languages');
    Route::get('language-data/{code}', 'BasicController@languageData');

    Route::namespace('Auth')->group(function () {
        Route::post('login', 'LoginController@login');
        Route::post('register', 'RegisterController@register');

        Route::post('password/email', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode');

        Route::post('password/reset', 'ResetPasswordController@reset');
    });


    Route::middleware('auth.api:sanctum')->name('user.')->prefix('user')->group(function () {
        Route::get('logout', 'Auth\LoginController@logout');
        Route::get('authorization', 'AuthorizationController@authorization')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatusApi'])->group(function () {
            Route::get('dashboard', function () {
                return auth()->user();
            });

            Route::post('profile-setting', 'UserController@submitProfile');
            Route::post('change-password', 'UserController@submitPassword');

            // Withdraw
            Route::get('withdraw/methods', 'UserController@withdrawMethods');
            Route::post('withdraw/store', 'UserController@withdrawStore');
            Route::post('withdraw/confirm', 'UserController@withdrawConfirm');
            Route::get('withdraw/history', 'UserController@withdrawLog');


            // Deposit
            Route::get('deposit/methods', 'PaymentController@depositMethods');
            Route::post('deposit/insert', 'PaymentController@depositInsert');
            Route::get('deposit/confirm', 'PaymentController@depositConfirm');

            Route::get('deposit/manual', 'PaymentController@manualDepositConfirm');
            Route::post('deposit/manual', 'PaymentController@manualDepositUpdate');

            Route::get('deposit/history', 'UserController@depositHistory');

            Route::get('transactions', 'UserController@transactions');

        });
    });
});

//-------------------Client Application-----------------------//
Route::namespace('Api\Client')->name('client.')->prefix('v1/client')->group(function () {
    //general
    Route::get('countries', 'GeneralController@countries');

    //Auth Routes
    Route::post('register', 'AuthController@register');
    Route::post('forget-password', 'AuthController@forgetPassword');
    Route::post('resend-code', 'AuthController@resendCode');
    Route::post('verify', 'AuthController@verify');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::post('login', 'AuthController@login');

    Route::middleware('auth.api:sanctum')->group(function () {
        Route::get('banners', 'GeneralController@banners');
        Route::get('booking-locations', 'GeneralController@bookingLocations');
        Route::post('fleet-types', 'GeneralController@fleetTypes');

        Route::prefix('direct-bookings')->group(function () {
            Route::post('search', 'BookingController@searchDirectBookings');
            Route::post('book', 'BookingController@bookDirectBooking');
            Route::get('seats/{trip}', 'BookingController@showSeats');

        });

        Route::prefix('special-bookings')->group(function () {
            Route::post('book', 'BookingController@bookSpecialBooking');
        });
        Route::get('tickets', 'BookingController@tickets');
        Route::get('cancel-ticket/{id}', 'BookingController@cancelTicket');

        Route::prefix('pocket')->group(function () {
            Route::get('get-amount', 'PocketController@getAmount');
            Route::get('manual-gateways', 'PocketController@manualGateways');
            Route::post('manual-charge', 'PocketController@manualCharge');
        });

        Route::get('history', 'HistoryController@getHistory');



    });
});
//-------------------Driver Application-----------------------//
Route::namespace('Api\Driver')->name('driver.')->prefix('v1/driver')->group(function () {

    //general
    Route::get('countries', 'GeneralController@countries');
    Route::get('fleet-types', 'GeneralController@fleetTypes');
    Route::get('routes', 'GeneralController@routes');
    Route::get('schedules', 'GeneralController@schedules');

    //Auth Routes
    Route::post('register', 'AuthController@register');
    Route::post('forget-password', 'AuthController@forgetPassword');
    Route::post('resend-code', 'AuthController@resendCode');
    Route::post('verify', 'AuthController@verify');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::post('login', 'AuthController@login');


    Route::middleware('auth.api:sanctum')->group(function () {
        Route::post('change-password', "AuthController@changePass");

        Route::get('banners', 'GeneralController@banners');

        Route::prefix('settings')->group(function () {
            Route::post('change-password', 'AuthController@changePassword');
        });

        Route::prefix('trips')->group(function () {
            Route::get('/', 'TripController@getTrips');
            Route::get('/all', 'TripController@getAlltrips');
            Route::get('/history', 'TripController@history');
            Route::get('/dates', 'TripController@dates');
            Route::get('/dead-line', 'TripController@deadLine');
            Route::get('show/{trip}', 'TripController@show');
            Route::post('update-status', 'TripController@updateStatus');
            Route::post('start', 'TripController@startTrip');
            Route::post('transfer', 'TripController@transferTrip');
            Route::get('finance-data', 'TripController@getDriverFinance');
            Route::post('edit-driver-trip', 'TripController@editDriverTripHistory');

        });

        Route::prefix('pocket')->group(function () {
            Route::get('get-amount', 'PocketController@getAmount');
        });
        Route::prefix('driver_deposit')->group(function () {
            Route::get('', 'DriverDepositController@index');
            Route::post('reject', 'DriverDepositController@reject');
            Route::post('approve', 'DriverDepositController@approve');
            Route::get('amount', 'DriverDepositController@getAmount');
        });
        Route::get('history', 'HistoryController@getHistory');
    });
});