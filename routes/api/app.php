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

Route::namespace('Api')->middleware('setLocale')->group(function () {

    Route::namespace('App')->group(function () {

        Route::namespace('General')->group(function () {

            Route::get('test', 'SettingController@bostaTest');
            Route::get('cities', 'SettingController@cities');
            Route::get('districts', 'SettingController@districts');

            Route::get('get-setting', 'SettingController@getSetting');

            Route::get('cities/{cityId}/districts', 'SettingController@districtsByCityId');

            Route::get('about', 'SettingController@getAbout');
            Route::get('terms', 'SettingController@getTerms');
            Route::get('policy', 'SettingController@getPrivacy');
            Route::get('contact', 'SettingController@getContact');
            Route::post('contact', 'SettingController@contact');
            Route::get('how_to_shop', 'SettingController@howToShop');

            Route::get('return_policy', 'SettingController@returnPolicy');

            Route::get('count_of_values', 'HelpController@countValues');
        });
        
        Route::namespace('Order')->group(function () {
            Route::get('coupons', 'CouponController@index');
            Route::get('coupons/{id}', 'CouponController@show');
        });

        Route::namespace('Favourite')->group(function () {
            Route::get('favourites', 'FavouriteController@index');
            Route::post('products/{product_detail_id}/fav', 'FavouriteController@fav');
        });

        Route::namespace('Cart')->group(function () {
            Route::get('cart', 'CartController@index');
            Route::post('cart/update_count', 'CartController@cartProductCount');
            Route::post('cart', 'CartController@store');
            Route::delete('delete_all_cart', 'CartController@deleteAllCart');
            Route::delete('delete_item/{id}', 'CartController@deleteItem');
        });

        Route::namespace('Order')->group(function () {
            Route::post('check_coupon', 'CouponController@checkCoupon');
        });

        Route::namespace('Home')->group(function () {
            Route::get('home', 'HomeController@index');
            Route::get('category_layers', 'HomeController@categoryLayers');
            Route::get('get_categories', 'HomeController@getCategories');
            Route::get('get_category', 'HomeController@getCategory');
            Route::get('get_category_data', 'HomeController@getCategoryData');
            Route::get('products/search', 'ProductController@search');
            Route::apiResource('products', 'ProductController')->except(['store', 'update', 'destroy']);
            Route::get('category/{category}/features', 'ProductController@getCategoryFeatures');
            Route::get('flash_sale', 'HomeController@flashSale');
            Route::get('offer/{id}/products', 'HomeController@offerProducts');
            Route::get('product_detail/{id}/reviews', 'ProductController@productDetailRates');

            Route::get('slider-details/{slider_id}', 'HomeController@slider_details');

        });
        Route::namespace('User')->group(function () {
            Route::post('register', 'AuthController@register');
            Route::post('verify', 'AuthController@verify');
            Route::post('resend_code', 'AuthController@resendCode');
            Route::post('login', 'AuthController@login');
            Route::post('forgot_password', 'AuthController@forgotPassword');
            Route::post('check_code', "AuthController@checkCode");
            Route::post('reset_password', "AuthController@resetPassword");

            Route::group(['middleware' => 'auth:api'], function () {
                // Logout
                Route::post('logout', 'AuthController@logout');
                // Profile
                Route::get('profile', 'AuthController@index');
                Route::post('profile', 'AuthController@store');
                Route::post('edit_password', 'AuthController@editPassword');
                Route::post('edit_phone', 'AuthController@editPhone');
                Route::post('check_phone_code', 'AuthController@checkPhoneCode');
                Route::post('update_phone', 'AuthController@updatePhone');
                Route::post('update_lang', 'AuthController@updateLang');
                Route::post('is_allow_notification', 'AuthController@isNotification');
                Route::delete('delete_my_account', 'AuthController@deleteMyAccount');

                Route::post('add-phone', 'AuthController@addPhone');
                Route::post('verify-phone', 'AuthController@verifyPhone');
            });
        });

        Route::group(['middleware' => 'auth:api'], function () {
            Route::namespace('Notification')->group(function () {
                Route::delete('delete_all_notifications', 'NotificationController@delete_all_notifications');
                Route::apiResource('notifications', 'NotificationController')->except(['update', 'store']);
            });

            Route::namespace('Reminder')->group(function () {
                Route::get('reminders', 'ReminderController@index');
                Route::post('flash_sale_products/{flash_sale_product_id}/remind_me', 'ReminderController@reminder');
            });
            // http: //app.bosta.co/api/v2/cities

            Route::namespace('Address')->group(function () {
                Route::post('addresses/{address_id}/is_default', 'AddressController@is_default');
                Route::apiResource('addresses', 'AddressController');
            });
            Route::namespace('Wallet')->group(function () {
                Route::get('wallet_transations', 'WalletController@index');
                Route::get('all_wallet_transations', 'WalletController@transactions');
                Route::post('charge', 'WalletController@charge');
                Route::post('withdrawals', 'WalletController@cacheOutRequest');
            });
            Route::namespace('ReturnProduct')->group(function () {
                Route::post('return_order', 'ReturnProductController@store');
            });
            Route::namespace('Order')->group(function () {
                // Route::post('check_coupon', 'CouponController@checkCoupon');
                Route::get('order', 'OrderController@index');
                Route::post('order', 'OrderController@store');
                Route::get('order/{id}', 'OrderController@show');
                Route::post('order/{id}/is_payment', 'OrderController@isPayment');
                Route::put('order/{id}/cancel', 'OrderController@cancel');
                Route::put('order/{id}/finished', 'OrderController@finished');
                Route::post('order/{order_id}/rate', 'RateController@store');
                Route::get('reviews', 'RateController@index');
                Route::post('reorder', 'OrderController@reorder');

                Route::get('order_status/{id}', 'OrderController@showOrderStatus');

            });
        });
    });
});

Route::get('generate_token/{order_id}', function ($order_id) {
    $order = \App\Models\OrderPriceDetail::where('order_id', $order_id)->first();
    $main_order = \App\Models\Order::findOrFail($order_id);
    $response_token = Http::withHeaders([
        'Content-Type'  => 'application/json',
    ])->post('https://accept.paymob.com/api/auth/tokens', [
        'username'      => '01022529304',
        'password'      => 'Davina@@123456789',
        'api_key'       => 'ZXlKaGJHY2lPaUpJVXpVeE1pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SndjbTltYVd4bFgzQnJJam8xTlRjd09EQXNJbTVoYldVaU9pSXhOamN3TWpNME16azJMakExTURFeU55SXNJbU5zWVhOeklqb2lUV1Z5WTJoaGJuUWlmUS5ONmVueWZ3eEFCbXI5NHpRR1QxdXZtVHBCRm8zd1V1SWdJYndla1J5bUNBTGUyM0M1bmU4TllEckpFcTBxa09kSExrWGZCRkdfYnBhRTRUX1BfdFhCUQ==',

    ]);


    $get_last_token = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post('https://accept.paymobsolutions.com/api/ecommerce/orders', [
        'auth_token'            => $response_token->json()['token'],
        'amount_cents'          => (int)($order->total_price * 100),
        'delivery_needed'       => 'false',
        'items'                 => [],

    ]);
    // dd($get_last_token->json()['id'] ,$response_token->json()['token']);
    // dd();

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post('https://accept.paymobsolutions.com/api/acceptance/payment_keys', [
        'auth_token'            => $response_token->json()['token'],
        'amount_cents'          => (int)($order->total_price * 100),
        'order_id'              => $get_last_token->json()['id'],
        "billing_data" => [ // put your client information
            "apartment" => "802",
            "email" => @$main_order->client->email ?? 'test',
            "floor" => "123",
            "first_name" => @$main_order->client->fullname ?? 'test',
            "street" => "st.mansoura",
            "building" => "159",
            "phone_number" => @$main_order->client->phone ?? '0123456789',
            "shipping_method" => "PKG",
            "postal_code" => "14789",
            "city" => "mansoura",
            "country" => "egypt",
            "last_name" => @$main_order->client->fullname ?? 'test',
            "state" => "daquhlya",
        ],
        'currency'              => 'EGP',
        'integration_id'        => '2915271',
        'mobile_integration_id'        => '2915270',
        'expiration'            => '3600',
        'online_iframe'    => '684672',
        'mobile_iframe'    => '684672', //         'currency'              => 'EGP',
        // 'integration_id'        => '2897478',
        // 'expiration'            => '3600',

    ]);
    // dd($response);


    return response()->json(['data' => $response->json(), 'status' => 'sucess', 'message' => '', 'extra_data' => [
        'online_integration_id'        => '2915271'/* '2897478'*/,
        'mobile_integration_id'        => '2915270',
        'expiration'            => '3600',
        'online_iframe'    => '684671',
        'mobile_iframe'    => '684672',
    ]]);
});

// Route::get('generate_token', function (){
//     $response_token = Http::withHeaders([
//         'Content-Type'  => 'application/json',
//     ])->post('https://accept.paymob.com/api/auth/tokens', [
//         'username'      => '01022529304',
//         'password'      => 'Davina@@123456789',
//         'api_key'       => 'ZXlKMGVYQWlPaUpLVjFRaUxDSmhiR2NpT2lKSVV6VXhNaUo5LmV5SndjbTltYVd4bFgzQnJJam8xTlRjd09EQXNJbTVoYldVaU9pSnBibWwwYVdGc0lpd2lZMnhoYzNNaU9pSk5aWEpqYUdGdWRDSjkuTjBSUGx2NTZFeEVIZGtjdnJFWmwwS1ZPeUJadVRPWUt5V293MDV3MnF5QmJKV2dia21aazdCMFo2amRfSmpDQzBfQXBpREs2MkpBYnBlMlQ2LU9EelE=',
     
//     ]);
    
//     $get_last_token = Http::withHeaders([
//         'Content-Type' => 'application/json',
//     ])->post('https://accept.paymobsolutions.com/api/ecommerce/orders', [
//         'auth_token'            => $response_token->json()['token'],
//         'amount_cents'          => '100',
 
//     ]);
    
//     $response = Http::withHeaders([
//         'Content-Type' => 'application/json',
//     ])->post('https://accept.paymobsolutions.com/api/acceptance/payment_keys', [
//         'auth_token'            => $response_token->json()['token'],
//         'amount_cents'          => '100',
//         'order_id'              => $get_last_token->json()['id'],
//         "billing_data" => [ // put your client information
//                 "apartment" => "802",
//                 "email" => 'karim',
//                 "floor" => "123",
//                 "first_name" => 'mansoura',
//                 "street" => "st.mansoura",
//                 "building" => "159",
//                 "phone_number" => '01023419903',
//                 "shipping_method" => "",
//                 "postal_code" => "14789",
//                 "city" => "mansoura",
//                 "country" => "egypt",
//                 "last_name" => 'mamdouh',
//                 "state" => "daquhlya",
//             ],
//         'currency'              => 'EGP',
//         'integration_id'        => '557080',
//         'expiration'            => '3600',
 
//     ]);
    
    
//     return response()->json(['data'=>$response->json(),'status'=>'sucess','message'=>'']);

// });
