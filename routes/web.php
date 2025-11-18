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

// route untuk view
Route::get('/all-other-product', function () {
    return view('web.productsPage.MerchAllProductPage');
})->name('all-other-product');
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

// Route bagian cart
Route::get('/cart', 'Web\CartController@index')->name('cart.index')->middleware('auth');
Route::post('/cart/add/{productId}', 'Web\CartController@addToCart')->name('cart.add')->middleware('auth');
Route::delete('/cart/{cartItem}', 'Web\CartController@destroy')->name('cart.destroy')->middleware('auth');
Route::post('/cart/add-merch/{merchProductId}', 'Web\CartController@addMerchToCart')->name('cart.addMerch')->middleware('auth');
Route::post('/cart/update/{cartItem}', 'Web\CartController@updateQuantity')->name('cart.update')->middleware('auth');

// merch product route
Route::get('/merch-products/batch', 'Web\MerchProduct\GetMerchProduct')->name('merch.products.batch');
Route::get('/merch/{slug}', 'Web\MerchProduct\getDetail')->name('merch.products.detail');

//midtrans-callback
Route::post('/payments/midtrans-notification','Account\PaymentCallbackController@receive');

//Checkout
Route::post('/checkout/process', 'Web\CheckoutMerchController@process')->name('checkout.process');
Route::get('/checkout/success/{invoice}', 'Web\CheckoutMerchController@success')->name('checkout.success');
Route::post('/checkout/set-address', 'Web\CheckoutMerchController@setAddress')->name('checkout.set-address');

// List semua provinsi
Route::get('/lokasi/provinsi', 'ProvinsiController@getAll')->name('lokasi.provinsi');

// List kabupaten berdasarkan provinsi
Route::get('/lokasi/kabupaten/{provinsi_id}', 'KabupatenController@getByProvinsi')->name('lokasi.kabupaten');

// List kecamatan berdasarkan kabupaten
Route::get('/lokasi/kecamatan/{kabupaten_id}', 'KecamatanController@getByKabupaten')->name('lokasi.kecamatan');

Route::post('/alamat/store', 'UserAddressController@store')->name('alamat.store');
Route::get('/alamat/refresh', 'UserAddressController@refreshList')->name('alamat.refresh');
