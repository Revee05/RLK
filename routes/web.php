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
// sample routes
Route::get('/cart', function () {
    return view('web.cart');
});
Route::get('/all-other-product', function () {
    return view('web.productsPage.MerchAllProductPage');
})->name('all-other-products');
Route::get('/detail-products', function () {
    return view('web.productsPage.MerchDetailProductPage');
})->name('detail-products');

// prod routes

Route::get('/','Web\HomeController@index')->name('home');
Route::get('/lelang','Web\HomeController@lelang')->name('lelang');
Route::get('/blogs','Web\BlogController@index')->name('blogs');
Route::get('/galeri-kami','Web\HomeController@galeriKami')->name('galeri.kami');
Route::post('/new/login', 'Auth\\LoginController@postLogin')->name('new.login');
Route::get('/products/search','Web\HomeController@search')->name('web.search');
Route::post('/bid/messages', 'Web\ChatsController@sendMessage');
Route::get('/checkout', 'Web\CheckoutMerchController@index')->name('checkout.index');
Route::get('/{slug}','Web\HomeController@detail')->name('detail');
Route::get('/page/{slug}','Web\HomeController@page')->name('web.page');
Route::get('/blog/{slug}','Web\BlogController@detail')->name('web.blog.detail');
Route::get('/bid/messages/{slug}', 'Web\ChatsController@fetchMessages');
Route::get('/category/{slug}','Web\HomeController@category')->name('products.category');
Route::get('/seniman/{slug}','Web\HomeController@seniman')->name('products.seniman');

// merch product route
Route::get('/merch-products/batch', 'Web\MerchProduct\GetMerchProduct')->name('merch.products.batch');
Route::get('/merch/{slug}', 'Web\MerchProduct\getDetail')->name('merch.products.detail');

//midtrans-callback
Route::post('/payments/midtrans-notification','Account\PaymentCallbackController@receive');

//Checkout
Route::post('/checkout/process', 'Web\CheckoutMerchController@process')->name('checkout.process');
Route::get('/checkout/success/{invoice}', 'Web\CheckoutMerchController@success')->name('checkout.success');
Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');

// API untuk fetch lokasi (dipakai AJAX di form)
Route::get('/get-kabupaten/{id}', function($id){
    return \App\Kabupaten::where('provinsi_id', $id)->get();
});

Route::get('/get-kecamatan/{id}', function($id){
    return \App\Kecamatan::where('kabupaten_id', $id)->get();
});

Route::get('/get-desa/{id}', function($id){
    return \App\Desa::where('kecamatan_id', $id)->get();
});
