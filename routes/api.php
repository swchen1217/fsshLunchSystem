<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

Route::get('/', function () {
    return response()->json(['success' => true]);
});

Route::get('/ip', function () {
    return response()->json([
        'HTTP_CLIENT_IP' =>$_SERVER['HTTP_CLIENT_IP']??'',
        'HTTP_X_FORWARDED_FOR'=>$_SERVER['HTTP_X_FORWARDED_FOR']??'',
        'HTTP_X_FORWARDED'=>$_SERVER['HTTP_X_FORWARDED']??'',
        'HTTP_X_CLUSTER_CLIENT_IP'=>$_SERVER['HTTP_X_CLUSTER_CLIENT_IP']??'',
        'HTTP_FORWARDED_FOR'=>$_SERVER['HTTP_FORWARDED_FOR']??'',
        'HTTP_FORWARDED'=>$_SERVER['HTTP_FORWARDED']??'',
        'REMOTE_ADDR'=>$_SERVER['REMOTE_ADDR']??'',
        'HTTP_VIA'=>$_SERVER['HTTP_VIA']??'']);
});

Route::group([
    'prefix' => 'oauth'
], function ($router) {
    Route::post('token', 'AuthController@createToken');
    Route::delete('token/{tokenId}', 'AuthController@revokeToken');
    Route::post('verify', 'AuthController@verify');
    Route::post('user', 'AuthController@user');
});

Route::group([
    'prefix' => 'dish'
], function ($router) {
    Route::get('/', 'DishController@getDish');
    Route::get('/{dish_id}', 'DishController@getDishById');
    // TODO
    //Route::post('/', '');
    //Route::put('/{dish_id}', '');
    //Route::delete('/{dish_id}', '');
});

// TODO
/*Route::group([
    'prefix' => 'sale'
], function ($router) {
    Route::get('/', '');
    Route::get('/id/{sale_id}', '');
    Route::get('/date/{date}', '');
    Route::post('/', '');
    Route::put('/{sale_id}', '');
    Route::delete('/{sale_id}', '');
});

Route::group([
    'prefix' => 'balance'
], function ($router) {
    Route::get('/{account}', '');
    Route::get('/log/{account}', '');
    Route::post('/top-up', '');
    Route::post('/refund', '');
});

Route::group([
    'prefix' => 'user'
], function ($router) {
    Route::get('/', '');
    Route::get('/account/{account}', '');
    Route::get('/class/{class}', '');
    Route::post('/', '');
    Route::put('/{account}', '');
    Route::delete('/{account}', '');
});

Route::group([
    'prefix' => 'order'
], function ($router) {
    Route::get('/', '');
    Route::get('/id/{order_id}', '');
    Route::get('/user/{user_id}', '');
    Route::get('/dish/{dish_id}', '');
    Route::get('/date/{date_id}', '');
    Route::get('/manufacturer/{manufacturer_id}', '');
    Route::get('/class/{class_id}', '');
    Route::post('/', '');
    Route::put('/{order_id}', '');
    Route::delete('/{order_id}', '');
});

Route::group([
    'prefix' => 'manufacturer'
], function ($router) {
    Route::get('/', '');
    Route::post('/', '');
    Route::put('/{manufacturer_id}', '');
    Route::delete('/{manufacturer_id}', '');
});

Route::group([
    'prefix' => 'rating'
], function ($router) {
    Route::get('/', '');
    Route::get('/{dish_id}', '');
    Route::post('/', '');
});
*/

/*

Route::group([
    'prefix' => ''
], function ($router) {
    Route::get('/{_id}', '');
    Route::post('/', '');
    Route::put('/{_id}', '');
    Route::delete('/{_id}', '');
});

*/
