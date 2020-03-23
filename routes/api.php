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

Route::get('/', 'GeneralController@isOk');

Route::get('/ip', 'GeneralController@ip');

Route::group([
    'prefix' => 'oauth'
], function ($router) {
    Route::post('token', 'AuthController@createToken');
    Route::delete('token/{tokenId}', 'AuthController@revokeToken');
    Route::post('verify', 'AuthController@verifyCommit');
    Route::post('user', 'AuthController@user');
});

Route::group([
    'prefix' => 'dish'
], function ($router) {
    Route::get('/', 'DishController@getDish')->middleware(['permission2:dish.read']);
    Route::get('/{dish_id}', 'DishController@getDishById')->middleware(['permission2:dish.read']);
    Route::post('/', 'DishController@newDish')->middleware(['permission2:dish.modify.create']);
    Route::post('/image/{dish_id}', 'DishController@image')->middleware(['permission2:dish.modify.create|dish.modify.update']);
    Route::patch('/{dish_id}', 'DishController@editDish')->middleware(['permission2:dish.modify.update']);
    Route::delete('/{dish_id}', 'DishController@removeDish')->middleware(['permission2:dish.modify.delete']);
});

Route::group([
    'prefix' => 'sale'
], function ($router) {
    Route::get('/', 'SaleController@getAll');
    Route::get('/id/{sale_id}', 'SaleController@getById');
    Route::get('/date/{date}', 'SaleController@getBySaleDate');
    Route::post('/', 'SaleController@create')->middleware(['permission2:sale.modify.create']);
    Route::patch('/{sale_id}', 'SaleController@edit')->middleware(['permission2:sale.modify.update']);
    Route::delete('/{sale_id}', 'SaleController@remove')->middleware(['permission2:sale.modify.delete']);
});

Route::group([
    'prefix' => 'pswd'
], function ($router) {
    Route::post('/account', 'PswdController@account');
    Route::post('/forget', 'PswdController@forget');
    Route::post('/token', 'PswdController@token');
});

Route::group([
    'prefix' => 'balance'
], function ($router) {
    Route::get('/today', 'BalanceController@getToday')->middleware(['permission2:balance.read.money']);
    Route::get('/{account}', 'BalanceController@getByAccount')->middleware(['permission2:balance.read.self.money']);
    Route::get('/log/{account}', 'BalanceController@getLogByAccount')->middleware(['permission2:balance.read.self.log']);
    Route::post('/top-up', 'BalanceController@topUp')->middleware(['permission2:balance.modify.top-up']);
    Route::post('/deduct', 'BalanceController@deduct')->middleware(['permission2:balance.modify.deduct']);
});

/*Route::group([
    'prefix' => 'user'
], function ($router) {
    Route::get('/', '');
    Route::get('/account/{account}', '');
    Route::get('/class/{class}', '');
    Route::post('/', '');
    Route::put('/{account}', '');
    Route::delete('/{account}', '');
});*/

Route::group([
    'prefix' => 'order'
], function ($router) {
    Route::get('/', 'OrderController@getAll');
    Route::get('/id/{order_id}', 'OrderController@getById');
    Route::get('/user/{user_id}', 'OrderController@getByUser');
    Route::get('/sale/{sale_id}', 'OrderController@getBySale');
    Route::get('/date/{date_id}', 'OrderController@getByDate');
    Route::get('/manufacturer/{manufacturer_id}', 'OrderController@getByManufacturer');
    Route::get('/class/{class}', 'OrderController@getByClass');
    Route::get('/class/{class}/today', 'OrderController@getTodayByClass');
    Route::get('/class/info/today', 'OrderController@getInfoToday')->middleware(['permission2:order.read']);
    Route::post('/', 'OrderController@create')->middleware(['permission2:order.modify.create.self']);
    //Route::patch('/{order_id}', 'OrderController@edit');
    Route::delete('/{order_id}', 'OrderController@remove')->middleware(['permission2:order.modify.delete.self']);
});

Route::group([
    'prefix' => 'manufacturer'
], function ($router) {
    Route::get('/', 'ManufacturerController@get');
    Route::post('/', 'ManufacturerController@create')->middleware(['permission2:manufacturer.modify.create']);
    Route::patch('/{manufacturer_id}', 'ManufacturerController@edit')->middleware(['permission2:manufacturer.modify.update']);
    Route::delete('/{manufacturer_id}', 'ManufacturerController@remove')->middleware(['permission2:manufacturer.modify.delete']);
});

/*Route::group([
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
