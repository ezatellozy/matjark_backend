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
|*/

Route::namespace('Api\Provider')->prefix('provider')->middleware('setLocale')->group(function () {

    Route::namespace('Auth')->group(function () {
        Route::post('login', 'AuthController@login')->name('provider.login');
    });

    
    Route::group(['middleware' => ['auth:api']], function () {

        Route::namespace('Auth')->group(function () {
            Route::post('logout', 'AuthController@logout');
        });

        Route::namespace('Profile')->group(function () {
            Route::get('profile', 'ProfileController@index');
            Route::post('profile', 'ProfileController@update');
            Route::post('update_password', 'ProfileController@updatePassword');
        });

        Route::namespace('Admin')->group(function () {
            Route::apiResource('admins', 'AdminController');
        });

        Route::namespace('Country')->group(function () {
            Route::apiResource('countries', 'CountryController');
            Route::get('countries/{country}/cities', 'CountryController@getCities');
            Route::get('countries/{country}/cities_without_pagination', 'CountryController@getCitiesByCountryWithoutPagination');
            Route::get('countries_without_pagination', 'CountryController@getCountriesWithoutPagination');
        });

        Route::namespace('City')->group(function () {
            Route::apiResource('cities', 'CityController');
            Route::get('cities_without_pagination', 'CityController@getCitiesWithoutPagination');
        });

        Route::namespace('Category')->group(function () {
            Route::apiResource('categories', 'CategoryController');
            Route::get('category/parents', 'CategoryController@getAllParentsCategory');
            Route::get('category/children', 'CategoryController@getAllChildrenCategory');
            Route::get('category/{category}/features', 'CategoryController@getCategoryFeatures');
            Route::get('category/features', 'CategoryController@getCategoriesFeatures');
            Route::get('category/all', 'CategoryController@getAllCategories');
            Route::get('category/{category}/third_level', 'CategoryController@getAllThirdLevelCategories');
            Route::get('category/treet', 'CategoryController@categoryTrees');
            Route::get('category/{category}/last', 'CategoryController@getLastCategory');
            Route::get('last-categories','CategoryController@LastCategories');
        });

        Route::namespace('Color')->group(function () {
            Route::apiResource('colors', 'ColorController');
            Route::get('colors_without_pagination', 'ColorController@getColorsWithoutPagination');
        });

        Route::namespace('Size')->group(function () {
            Route::apiResource('sizes', 'SizeController');
            Route::get('sizes_without_pagination', 'SizeController@getSizesWithoutPagination');
        });

        Route::namespace('Product')->group(function () {
            Route::apiResource('products', 'ProductController');
            Route::delete('products/{product}/details/images/{image}', 'ProductController@deleteProductDetailsImage');
            Route::delete('products/{product}/details/{detail}', 'ProductController@deleteProductDetail');
            Route::patch('products/{product}/details/{detail}', 'ProductController@updateQuantity');
            Route::get('product/without_paginate', 'ProductController@productsWithoutPaginate');
            Route::get('products/{product}/show', 'ProductController@showProduct');
        });

        Route::namespace('Setting')->group(function () {
            Route::apiResource('settings', 'SettingController');
        });

        Route::namespace('About')->group(function () {
            Route::apiResource('about', 'AboutController');
        });

        Route::namespace('Term')->group(function () {
            Route::apiResource('term', 'TermController');
        });

        Route::namespace('Privacy')->group(function () {
            Route::apiResource('privacy', 'PrivacyController');
        });

        Route::namespace('Slider')->group(function () {
            Route::apiResource('slider', 'SliderController');
        });

        Route::namespace('Feature')->group(function () {
            Route::apiResource('feature', 'FeatureController');
            Route::post('feature/{feature}/values', 'FeatureController@addValueToFeature');
            Route::delete('feature/{feature}/values/{value}', 'FeatureController@deleteValue');
        });

        Route::namespace('Offer')->group(function () {
            Route::apiResource('offer', 'OfferController');
        });

        Route::namespace('FlashSale')->group(function () {
            Route::apiResource('flash-sale', 'FlashSaleController');
        });

        Route::namespace('Coupon')->group(function () {
            Route::get('coupon_addition_data/{id}','CouponController@couponAdditionData');
            Route::get('coupon_addition_data/{id}/orders','CouponController@couponAdditionDataOrders');

            Route::apiResource('coupon', 'CouponController');

        });

        Route::namespace('Client')->group(function () {
            Route::apiResource('client', 'ClientController');
            Route::get('clients/without-pagination', 'ClientController@clientsWithoutPagination');
                         Route::get('client/{client_id}/orders' ,'ClientController@orders');

        });

        Route::namespace('Wallet')->prefix('wallet')->group(function () {
            Route::get('withdrawal', 'WithdrawalController@index');
            Route::get('withdrawal/{withdrawal}', 'WithdrawalController@show');
            Route::put('withdrawal/{withdrawal}', 'WithdrawalController@changeStatus');
            Route::put('client/{client}/charge', 'WalletController@charge');
            Route::get('client/{client}/transactions', 'WalletController@transactions');
        });

        Route::namespace('Order')->group(function () {
            Route::apiResource('order', 'OrderController');
            Route::put('order/{order}/change_status', 'OrderController@changeStatus');

            Route::get('order-items', 'OrderController@order_items');
        });

        Route::namespace('Notification')->group(function () {
            Route::get('unread-notification-count','NotificationController@unreadNotificationCount');
            Route::apiResource('notifications', 'NotificationController')->except('update');
        });

        Route::namespace('Contact')->group(function () {
            Route::apiResource('contacts', 'ContactController')->except(['store', 'update']);
            Route::post('contacts/{contact}/reply', 'ContactController@reply');
        });

        Route::namespace('Rate')->group(function () {
            Route::get('rates', 'RateController@index');
            Route::get('rates/{id}', 'RateController@show');
            Route::patch('rates/change-status', 'RateController@changeStatus');
            Route::delete('rates/{rate}', 'RateController@destroy');
            Route::delete('rates/{rate}/images/{image}', 'RateController@deleteImage');
        });

        Route::namespace('InventoryTracking')->group(function () {
            Route::get('stock-tracking', 'InventoryTrackingController@index');
        });

        Route::namespace('Home')->group(function () {
            Route::get('home', 'HomeController@index');
        });

        Route::namespace('ReturnOrder')->group(function () {
            Route::get('return-orders', 'ReturnOrderController@index');
            Route::get('return-order/{id}', 'ReturnOrderController@show');
            Route::get('return-order/{id}/return-order-product', 'ReturnOrderController@getReturnOrderProductByStatus');
            Route::patch('return-order/change-status/first-step', 'ReturnOrderController@changeStatus');
            Route::patch('return-order/change-status/second-step', 'ReturnOrderController@changeReturnOrderProductStatus');
        });

        // Route::namespace('Statistic')->group(function () {
        //     Route::apiResource('statistics', 'StatisticController')->only('index');
        // });
    });
});
