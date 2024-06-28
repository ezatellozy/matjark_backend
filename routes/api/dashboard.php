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

Route::namespace('Api\Dashboard')->prefix('dashboard')->middleware('setLocale')->group(function () {

    Route::namespace('Auth')->group(function () {
        Route::post('login', 'AuthController@login')->name('dashboard.login');
    });

    // Route::group(['middleware' => ['auth:api','CustomPermission']], function () {
    Route::group(['middleware' => ['auth:api','CustomPermission']], function () {


        Route::namespace('Auth')->group(function () {
            Route::post('logout', 'AuthController@logout');
        });

        Route::namespace('Role')->group(function () {
            Route::apiResource('role', 'RoleController');
            Route::get('role_not_paginated', 'RoleController@indexNotPaginated');
            Route::get('role-names', 'RoleController@role_names');
        });

        Route::namespace('Permission')->group(function () {
            Route::apiResource('permission', 'PermissionController');
            Route::get('permission_not_paginated', 'PermissionController@indexNotPaginated');
            Route::get('side-bar-permissions', 'PermissionController@sideBarPermission')->name('side-bar-permissions');
            Route::post('update-all-permissions', 'PermissionController@update_all_permissions');
        });

        Route::namespace('Profile')->group(function () {
            Route::get('profile', 'ProfileController@index')->name('profile.index');
            Route::post('profile', 'ProfileController@update')->name('profile.update');
            Route::post('update_password', 'ProfileController@updatePassword')->name('profile.update_password');
            Route::post('update-company-profile', 'ProfileController@update_company_profile')->name('profile.update_company_profile');
        });

        Route::namespace('Admin')->group(function () {
            Route::apiResource('admins', 'AdminController');
            Route::get('get_my_permissions', 'AdminController@getMyPermissions')->name('admins.get_my_permissions');

        });

        Route::namespace('Cart')->group(function () {
            Route::apiResource('cart', 'CartController');
        });

        Route::namespace('Provider')->group(function () {
            Route::apiResource('providers', 'ProviderController');
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
            Route::get('categories-without-pagination','CategoryController@CategoriesWithOutPagination');

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
            Route::patch('products/{product}/details/{detail}', 'ProductController@updateQuantity');
            Route::get('product/without_paginate', 'ProductController@productsWithoutPaginate');
            Route::get('products/{product}/show', 'ProductController@showProduct');
            Route::get('product-summary/{product}', 'ProductController@summaryProduct');


            Route::get('product-details-v2/{product}', 'ProductController@product_details_v2');
            // Route::get('product-orders/{product}', 'ProductController@productOrders');

            Route::patch('product/{product_id}/toggle-active-product', 'ProductController@toggleActive');


            Route::delete('products/{product_id}/details/{color_id}', 'ProductController@deleteProductDetail');
            Route::delete('products/{product_id}/images/{image_id}', 'ProductController@deleteProductDetailsImage');

            Route::delete('products/{product_id}/details/{color_id}/feature/{feature_id}', 'ProductController@deleteProductDetailsFeature');
            Route::delete('products/{product_id}/details/{color_id}/size/{size_id}', 'ProductController@deleteProductDetailsSize');

            ////
            Route::get('statistics-product/{product}', 'ProductController@product_statistics');

            Route::get('get-active-products', 'ProductController@getActiveProducts');



        });

        Route::namespace('Setting')->group(function () {
            Route::apiResource('settings', 'SettingController');
        });

        Route::namespace('StaticPage')->group(function () {
            Route::apiResource('static_page_meta_data', 'StaticPageMetaDataController')->only('store', 'show');
        });

        Route::namespace('About')->group(function () {
            Route::apiResource('about', 'AboutController');
            // Route::apiResource('about_meta_data', 'AboutMetaDataController');
        });

        Route::namespace('CommonQuestion')->group(function () {
            Route::apiResource('commonQuestions', 'CommonQuestionController');
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

            Route::get('get-features-without-paginations', 'FeatureController@getFeaturesWithoutPagination');
        });

        Route::namespace('Offer')->group(function () {
            Route::apiResource('offer', 'OfferController');
            Route::get('get-active-offers', 'OfferController@getActiveOffers');

        });

        Route::namespace('FlashSale')->group(function () {
            Route::apiResource('flash-sale', 'FlashSaleController');
            Route::get('flash-sale-details/{id}', 'FlashSaleController@details');

        });

        Route::namespace('Coupon')->group(function () {
            Route::get('coupon_addition_data/{id}','CouponController@couponAdditionData');
            Route::get('coupon_addition_data/{id}/orders','CouponController@couponAdditionDataOrders');

            Route::apiResource('coupon', 'CouponController');

        });

        Route::namespace('Client')->group(function () {
            Route::apiResource('client', 'ClientController');
            Route::get('clients/without-pagination', 'ClientController@clientsWithoutPagination');
            Route::get('client/{client_id}/orders' ,'ClientController@orders')->name('client.get_orders');

            Route::patch('client/{user_id}/toggle-active-user', 'ClientController@toggleActive');
            Route::patch('client/{user_id}/toggle-ban-user', 'ClientController@toggleBan');

        });

        Route::namespace('Wallet')->prefix('wallet')->group(function () {
            Route::get('withdrawal', 'WithdrawalController@index')->name('withdrawal.index');
            Route::get('withdrawal/{withdrawal}', 'WithdrawalController@show')->name('withdrawal.show');
            Route::put('withdrawal/{withdrawal}', 'WithdrawalController@changeStatus')->name('withdrawal.change_status');
            Route::put('client/{client}/charge', 'WalletController@charge')->name('client.charge');
            Route::get('client/{client}/transactions', 'WalletController@transactions')->name('client.transactions');
        });

        Route::namespace('Order')->group(function () {
            Route::apiResource('order', 'OrderController');
            Route::put('order/{order}/change_status', 'OrderController@changeStatus')->name('order.change_status');
        });

        Route::namespace('Notification')->group(function () {
            Route::get('unread-notification-count','NotificationController@unreadNotificationCount');
            Route::apiResource('notifications', 'NotificationController')->except('update');
        });

        Route::namespace('Contact')->group(function () {
            Route::apiResource('contacts', 'ContactController')->except(['store', 'update']);
            Route::post('contacts/{contact}/reply', 'ContactController@reply')->name('contacts.reply');
        });

        Route::namespace('Rate')->group(function () {


            Route::get('rated-users', 'RateController@rated_users')->name('rates.users');

            Route::get('rates', 'RateController@index')->name('rates.index');
            Route::get('rates/{id}', 'RateController@show')->name('rates.show');
            Route::patch('rates/change-status', 'RateController@changeStatus')->name('rates.change_status');
            Route::delete('rates/{rate}', 'RateController@destroy')->name('rates.delete');
            Route::delete('rates/{rate}/images/{image}', 'RateController@deleteImage')->name('rates.delete_image');
        });

        Route::namespace('InventoryTracking')->group(function () {
            Route::get('stock-tracking', 'InventoryTrackingController@index')->name('stock-tracking.index');
        });

        Route::namespace('Home')->group(function () {
            Route::get('home', 'HomeController@index')->name('home.index');
        });

        Route::namespace('ReturnOrder')->group(function () {
            Route::get('return-orders', 'ReturnOrderController@index')->name('return-orders.index');
            Route::get('return-order/{id}', 'ReturnOrderController@show')->name('return-order.show');
            Route::get('return-order/{id}/return-order-product', 'ReturnOrderController@getReturnOrderProductByStatus')->name('return-order.get_return_order_product_by_status');
            Route::patch('return-order/change-status/first-step', 'ReturnOrderController@changeStatus')->name('return-order.change_status');
            Route::patch('return-order/change-status/second-step', 'ReturnOrderController@changeReturnOrderProductStatus')->name('return-order.change_return_order_product_status');
        });

        // Route::namespace('Statistic')->group(function () {
        //     Route::apiResource('statistics', 'StatisticController')->only('index');
        // });


        Route::namespace('Report')->group(function () {
            Route::get('sales-report', 'ReportController@sales')->name('sales-report.index');
            Route::get('most-sales-products-report', 'ReportController@products')->name('most-sales-products-repor.index');
            Route::get('clients-report', 'ReportController@clients')->name('clients-report.index');
            Route::get('most-orders-dates-report', 'ReportController@most_orders_dates')->name('most-orders-dates-report.index');
        });


        Route::namespace('NewsLetter')->group(function () {
            Route::get('news-letter', 'NewsLetterController@home')->name('news-letter.index');
        });

    });
});
