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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API untuk cascade dropdown location
Route::get('/cities/{province_id}', function($province_id) {
    return \App\City::where('province_id', $province_id)->orderBy('name', 'asc')->get(['id', 'name']);
});

Route::get('/districts/{city_id}', function($city_id) {
    return \App\District::where('city_id', $city_id)->orderBy('name', 'asc')->get(['id', 'name']);
});
