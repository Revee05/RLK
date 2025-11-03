<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require_once  __DIR__ . "/admin.php";
require_once  __DIR__ . "/account.php";

Auth::routes();

Route::get('/','Web\HomeController@index')->name('home');
Route::get('/lelang','Web\HomeController@lelang')->name('lelang');
Route::get('/blog/{slug}','Web\BlogController@detail')->name('web.blog.detail');
Route::get('/blogs','Web\BlogController@index')->name('blogs');
Route::get('/galeri-kami','Web\HomeController@galeriKami')->name('galeri.kami');
Route::get('/{slug}','Web\HomeController@detail')->name('detail');
Route::post('/new/login', 'Auth\\LoginController@postLogin')->name('new.login');
Route::get('/category/{slug}','Web\HomeController@category')->name('products.category');
Route::get('/seniman/{slug}','Web\HomeController@seniman')->name('products.seniman');
Route::get('/page/{slug}','Web\HomeController@page')->name('web.page');
Route::get('/products/search','Web\HomeController@search')->name('web.search');
Route::get('/bid/messages/{slug}', 'Web\ChatsController@fetchMessages');
Route::post('/bid/messages', 'Web\ChatsController@sendMessage');

//midtrans-callback
Route::post('/payments/midtrans-notification','Account\PaymentCallbackController@receive');

