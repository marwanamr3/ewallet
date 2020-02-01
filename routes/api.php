<?php

use Illuminate\Http\Request;

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

//--------------------------------
//Registeration and Login Routes:
//--------------------------------

Route::post('login', 'PassportController@login');
Route::post('register', 'PassportController@register');

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'PassportController@user');
    Route::get('/logout', 'PassportController@logout');



    Route::middleware('IsCustomer')->group(function () {


        Route::resource('/merchants', 'MerchantsController')->only(['index', 'show']);

        Route::get('/merchantSearch','MerchantsController@search');

        Route::post('services/{service}/subscribe', 'ServiceController@subscribeUser');

        Route::post('products/{product}/buy', 'ProductController@buy');

        Route::post('transfer', 'TransferController@store');

        //----------------
        //Recharge Routes:
        //----------------
        Route::post('recharge', 'RechargeController@store');
    });


    Route::middleware('IsMerchant')->group(function () {

        Route::post('services', 'ServiceController@store');
        Route::middleware('ownService')->group(function () {
            Route::put('services/{service}', 'ServiceController@update');
            Route::delete('services/{service}', 'ServiceController@destroy');
        });

        Route::post('products', 'ProductController@store');
        Route::middleware('ownProduct')->group(function () {
            Route::put('products/{product}', 'ProductController@update');
            Route::delete('products/{product}', 'ProductController@destroy');
        });
    });

    //-----------------
    //Merchant Routes:
    //-----------------
    Route::get('/merchants/{merchant}/services', 'MerchantsController@getMerchantServices');

    Route::get('/merchants/{merchant}/products', 'MerchantsController@getMerchantProducts');


    //----------------
    //Service Routes:
    //----------------
    Route::get('services/{service}', 'ServiceController@show');

    Route::get('/serviceSearch','ServiceController@search');

    //----------------
    //Product Routes:
    //----------------
    Route::get('products/{product}', 'ProductController@show');

    Route::get('/productSearch','ProductController@search');

    //-------------------
    //Transaction Routes:
    //-------------------
    Route::get('transactions', 'TransactionController@index');

    Route::get('/transactionSearch','transactionController@search');

    Route::get('transactions/{transaction}', 'TransactionController@show');

    Route::put('transactions/{transaction}', 'TransactionController@edit');

    //-----------------
    //Profile Routes:
    //-----------------
    Route::get('/profile', 'ProfilesController@viewProfile');

    Route::put('/profile', 'ProfilesController@updateProfile');

    Route::delete('/profile', 'ProfilesController@deleteProfile');

    //-----------------
    //Image Routes:
    //-----------------
    Route::get('/images/{path}', 'ImagesController@getImage');

});






