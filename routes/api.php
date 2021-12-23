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
    // shop
    Route::get('/t/{team}/shops', \App\Http\Controllers\ShopController::class.'@list');
    Route::get('/t/{team}/shops/total', \App\Http\Controllers\ShopController::class.'@total');
    Route::post('/t/{team}/shop/create', \App\Http\Controllers\ShopController::class. '@store');

    // device
    Route::get('/t/{team}/devices', \App\Http\Controllers\DeviceController::class.'@all');
    Route::get('/t/{team}/devices/total', \App\Http\Controllers\DeviceController::class.'@total');
    Route::post('/t/{team}/device/self-host/create', \App\Http\Controllers\DeviceController::class . '@createSelfHost');
});
