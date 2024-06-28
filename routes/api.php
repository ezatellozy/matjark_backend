<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request;

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
Route::namespace('Api')->middleware('setLocale')->group(function(){
    
    Route::namespace('User')->group(function(){
        
        Route::post('register', 'AuthController@signup');

        Route::post('login', 'AuthController@login');

        Route::post('verify', 'AuthController@confirm');

        Route::post('send_code','AuthController@sendCode');

        Route::post('check_code', "AuthController@checkCode");

        Route::post('reset_password', "AuthController@resetPassword");
        Route::post('update_location', "UserController@updateUserLocation");

        Route::group(['middleware' => 'auth:api'], function () {
            // Logout
            Route::post('logout', 'AuthController@logout');
            // Profile
            Route::get('profile', 'UserController@index');
            Route::post('profile', 'UserController@store');
            Route::post('edit_password', 'UserController@editPassword');

            // Chat
            Route::get('chats/{order_id}/{receiver_id}','ChatController@show');
            Route::apiResource('chats','ChatController')->only('index','store');
            Route::put('chats/{chat_id}/message_is_seen','ChatController@messageIsSeen');
            // Notification
            Route::apiResource('notifications','NotificationController')->only('index','show','destroy');

            // Wallet
            Route::apiResource('wallet_transfers','WalletTransfersController')->only('index','show','store');
            Route::get('wallet','WalletController@index');
            Route::get('my_ibans','WalletController@getIbans');
            Route::post('charge_wallet','WalletController@chargeWallet');
            Route::post('withdrawal_wallet','WalletController@withdrawalWallet');
        });
    });

    Route::namespace('SiteMap')->group(function(){
        Route::get('main_site_map', 'SiteMapController@mainSiteMap');
        Route::get('products_site_map', 'SiteMapController@productsSiteMap');
        Route::get('categories_site_map', 'SiteMapController@categoriesSiteMap');
        Route::get('about_site_map', 'SiteMapController@aboutSiteMap');
        Route::get('privacy_site_map', 'SiteMapController@privacySiteMap');
        Route::get('return_policy_site_map', 'SiteMapController@returnPolicySiteMap');
        Route::get('category/{category:slug}/products', 'SiteMapController@categoryProductsSiteMap');
    });
    

    Route::namespace('Help')->group(function(){
        // Country
        Route::get('countries', "CountryController@index");
        // City
        Route::get('country/{country_id}/cities', "CountryController@show");
        // About
        Route::get('about', 'HomeController@getAbout');
        
        // Policy
        Route::get('policy', 'HomeController@getPolicy');
        // Terms
        Route::get('terms', 'HomeController@getTerms');

        // Contact
        Route::get('contact', 'HomeController@getContact');
        // Contact Us & Complaints   // ->middleware('auth:api')
        Route::post('contact', 'HomeController@contact');

        // Slider
        Route::get('sliders','SliderController@index');

        // Car Types
        Route::get('car_types','HelpController@getCarTypes');

        // Selenders
        Route::get('get_selenders','HelpController@getSelenders');

        // Districts
        Route::get('districts','HelpController@getDistricts');

         // Available Days
         Route::get('available_days/{district_id}','HelpController@getAvailableDays');

          // Favorite Times
          Route::get('favorite_times/{available_day_id}','HelpController@getFavoriteTimes');
             // Main Categories
          Route::get('main_categories','HelpController@getMainCategories');
             // Sub Categories
          Route::get('sub_categories','HelpController@getSubCategories');
               // Second Sub Categories
          Route::get('second_sub_categories','HelpController@getSecondSubCategories');
          // Cart
          Route::post('add_to_cart','CartController@addToCart');

        // Delete Images
        Route::delete('delete_app_image/{image_id}','HomeController@deleteAppImage')->middleware("auth:api");

        // Search
        Route::get('search', 'HomeController@search');


        // Slider
        Route::get('sliders','SliderController@index');


    });
});
