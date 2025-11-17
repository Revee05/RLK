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
// sample routes dev 
Route::get('/cart', function () {
    return view('web.cart');
});

// route untuk view
Route::get('/all-other-product', function () {
    return view('web.productsPage.MerchAllProductPage');
})->name('all-other-product'); // untuk return view halaman produk merch


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
Route::get('/merch/{slug}', 'Web\MerchProduct\getDetail')->name('merch.products.detail');
Route::get('/merch-products/json', 'Web\MerchProduct\GetMerchProduct')->name('merch.products.json');

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
