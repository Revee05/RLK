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

use App\Http\Controllers\Web\CheckoutMerchController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\AdminPanduanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\AuctionHistoryController;
use App\Http\Controllers\UsersController;

require_once  __DIR__ . "/admin.php";
require_once  __DIR__ . "/account.php";


// =============================
// HOME & LANDING PAGE
// =============================
Route::get('/', 'Web\HomeController@index')->name('home');
Route::get('/perusahaan', 'Web\HomeController@perusahaan')->name('perusahaan');
Route::get('/tim', 'Web\HomeController@tim')->name('tim');
Route::get('/blogs', 'Web\BlogController@index')->name('blogs');
Route::get('/blog/{slug}', 'Web\BlogController@detail')->name('web.blog.detail');
Route::get('/page/{slug}', 'Web\HomeController@page')->name('web.page');
Route::get('/products/search', 'Web\HomeController@search')->name('web.search');
Route::get('/category/{slug}', 'Web\HomeController@category')->name('products.category');
Route::get('/detail/{slug}', 'Web\HomeController@detail')->name('detail');

// =============================
// AUTHENTIKASI
// =============================
Auth::routes();
Route::post('/new/login', 'Auth\\LoginController@postLogin')->name('new.login');

// =============================
// PEMBAYARAN (PAYMENT)
// =============================
Route::get('/pay', 'PaymentController@createInvoice')->name('pay');
Route::post('/payment/callback', 'PaymentController@callback');
Route::post('/payments/midtrans-notification', 'Account\PaymentCallbackController@receive');

// =============================
// CHECKOUT
// =============================
Route::get('/checkout', 'Web\CheckoutMerchController@index')->name('checkout.index');
Route::post('/checkout/process', 'Web\CheckoutMerchController@process')->name('checkout.process');
Route::get('/checkout/success/{invoice}', 'Web\CheckoutMerchController@success')->name('checkout.success');
Route::post('/checkout/set-address', 'Web\CheckoutMerchController@setAddress')->name('checkout.set-address');
Route::post('/checkout/shipping-cost', 'Web\CheckoutMerchController@getShippingCost')->name('checkout.shipping-cost');
Route::post('/checkout/pay', 'Web\PaymentController@payNow')->name('checkout.pay');
Route::get('/checkout/success', function () {
    return 'Pembayaran berhasil!';
})->name('checkout.success');
Route::get('/checkout/failed', function () {
    return 'Pembayaran gagal!';
})->name('checkout.failed');

// =============================
// LELANG (AUCTION)
// =============================
Route::get('/lelang', function () {
    return view('web.LelangPage.lelang');
})->name('lelang');
Route::get('/lelang-products/json', [\App\Http\Controllers\Web\LelangProduct\getAll::class, 'json'])->name('lelang.products.json');
Route::get('/lelang-categories', [\App\Http\Controllers\Web\LelangProduct\getCategory::class, 'LelangCategory'])->name('lelang.categories');
Route::get('/lelang/{slug}', [\App\Http\Controllers\Web\LelangProduct\getDetail::class, 'show'])->name('lelang.detail');
Route::post('/bid/messages', 'Web\ChatsController@sendMessage')->middleware('auth')->name('bid.send');
Route::get('/bid/messages/{slug}', 'Web\ChatsController@fetchMessages')->name('bid.fetch');
// Realtime state endpoint for reconciliation
Route::get('/bid/state/{slug}', 'Web\ChatsController@state')->name('bid.state');

// =============================
// MERCHANDISE PRODUCT
// =============================
Route::get('/all-other-product', function () {
    return view('web.productsPage.MerchAllProductPage');
})->name('all-other-product');
Route::get('/detail-products', function () {
    return view('web.productsPage.MerchDetailProductPage');
})->name('detail-products');
Route::get('/merch/categories', 'Web\MerchProduct\GetMerchCategory')->name('merch.categories');
Route::get('/merch/{slug}', 'Web\MerchProduct\getDetail')->name('merch.products.detail');
Route::get('/merch-products/json', 'Web\MerchProduct\GetMerchProduct')->name('merch.products.json');

// =============================
// SENIMAN
// =============================
Route::get('/seniman', 'Web\SenimanController@index')->name('seniman.index');
Route::get('/seniman/{slug}', 'Web\SenimanController@detail')->name('seniman.detail');
Route::get('/produk-seniman/{slug}', [\App\Http\Controllers\Web\SenimanController::class, 'detail'])->name('products.seniman');

// =============================
// CART
// =============================
Route::group(['middleware' => ['auth']], function () {
    Route::get('/cart', 'Web\CartController@index')->name('cart.index');
    Route::post('/cart/add-merch', 'Web\CartController@addMerchToCart')->name('cart.addMerch');
    Route::post('/cart/update/{cartItem}', 'Web\CartController@updateQuantity')->name('cart.update');
    Route::delete('/cart/{cartItem}', 'Web\CartController@destroy')->name('cart.destroy');
    Route::post('/cart/update-option/{id}', 'Web\CartController@updateOption')->name('cart.updateOption');
});

// =============================
// AUCTION HISTORY
// =============================
Route::group(['middleware' => ['auth']], function () {
    Route::get('/account/auction', [AuctionHistoryController::class, 'index'])->name('account.auction_history');
});

// =============================
// API LOKASI & ALAMAT
// =============================
Route::get('/get-kabupaten/{id}', function ($id) {
    return \App\Kabupaten::where('provinsi_id', $id)->get();
});
Route::get('/lokasi/province', 'LocationController@province')->name('lokasi.province');
Route::get('/lokasi/city/{province_id}', 'LocationController@city')->name('lokasi.city');
Route::get('/lokasi/district/{city_id}', 'LocationController@district')->name('lokasi.district');
Route::post('/alamat/store', 'UserAddressController@store')->name('alamat.store');
Route::get('/alamat/refresh', 'UserAddressController@refreshList')->name('alamat.refresh');

Route::post('/favorite/toggle', [\App\Http\Controllers\Web\FavoriteController::class, 'toggle'])
    ->name('favorite.toggle');


   Route::get('/account/favorites', [UsersController::class, 'favorites'])->name('account.favorites');

   Route::delete('/account/favorites/{id}', 'Account\FavoriteController@remove')
    ->name('account.favorites.remove');
// =============================
// PANDUAN (GUIDE)
// =============================
Route::get('/panduan', 'PanduanController@index')->name('panduan.index');
Route::get('/panduan/load/{slug}', 'PanduanController@loadPanduan');
