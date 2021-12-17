<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/shops', \App\Http\Controllers\ShopController::class.'@list');
    Route::post('/shop/create/check', \App\Http\Controllers\ShopController::class. '@createCheck');
    Route::get('/devices', \App\Http\Controllers\DeviceController::class.'@all');

    Route::post('/device/self-host', \App\Http\Controllers\DeviceController::class . '@createSelfHost');
});
