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
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\AuctionHistoryController;

require_once  __DIR__ . "/admin.php";
require_once  __DIR__ . "/account.php";

Auth::routes();
// sample routes
Route::get('/cart', function () {
    return view('web.cart');
});

// route untuk view produk merchandise
Route::get('/all-other-product', function () {
    return view('web.productsPage.MerchAllProductPage');
})->name('all-other-product');
Route::get('/detail-products', function () {
    return view('web.productsPage.MerchDetailProductPage');
})->name('detail-products');

// route untuk view produk lelang
Route::get('/lelang', function () {
    return view('web.LelangPage.lelang');
})->name('lelang');

//
Route::get('/pay', 'PaymentController@createInvoice')->name('pay');
Route::post('/payment/callback', 'PaymentController@callback');
Route::get('/','Web\HomeController@index')->name('home');
Route::get('/blogs','Web\BlogController@index')->name('blogs');
Route::get('/galeri-kami','Web\HomeController@galeriKami')->name('galeri.kami');
Route::post('/new/login', 'Auth\\LoginController@postLogin')->name('new.login');
Route::get('/products/search','Web\HomeController@search')->name('web.search');
<<<<<<< HEAD
Route::post('/bid/messages', 'Web\ChatsController@sendMessage')->name('bid.send');
=======
>>>>>>> d7ee93efee9b0fa4e4b5ea5a6fab712e004c3718
Route::get('/checkout', 'Web\CheckoutMerchController@index')->name('checkout.index');

// Seniman routes
Route::get('/seniman', 'Web\SenimanController@index')->name('seniman.index');
Route::get('/seniman/{slug}', 'Web\SenimanController@detail')->name('seniman.detail');
Route::get('/produk-seniman/{slug}', [\App\Http\Controllers\Web\SenimanController::class, 'detail'])->name('products.seniman');

<<<<<<< HEAD
Route::get('/{slug}','Web\HomeController@detail')->name('detail');
Route::get('/page/{slug}','Web\HomeController@page')->name('web.page');
Route::get('/blog/{slug}','Web\BlogController@detail')->name('web.blog.detail');
Route::get('/bid/messages/{slug}', 'Web\ChatsController@fetchMessages')->name('bid.fetch');
=======
// Detail halaman produk lelang
Route::get('/lelang-products/json', [\App\Http\Controllers\Web\LelangProduct\getAll::class, 'json'])->name('lelang.products.json');
Route::get('/lelang-categories', [\App\Http\Controllers\Web\LelangProduct\getCategory::class, 'LelangCategory'])->name('lelang.categories');
Route::get('/lelang/{slug}', [\App\Http\Controllers\Web\LelangProduct\getDetail::class, 'show'])->name('lelang.detail');
Route::post('/bid/messages', 'Web\ChatsController@sendMessage');
Route::get('/bid/messages/{slug}', 'Web\ChatsController@fetchMessages');


// halaman home/beranda
Route::get('/page/{slug}','Web\HomeController@page')->name('web.page');
Route::get('/{slug}','Web\HomeController@detail')->name('detail');
>>>>>>> d7ee93efee9b0fa4e4b5ea5a6fab712e004c3718
Route::get('/category/{slug}','Web\HomeController@category')->name('products.category');

// Blog detail di halaman blog detail
Route::get('/blog/{slug}','Web\BlogController@detail')->name('web.blog.detail');


Route::group(['middleware' => ['auth']], function () {
    // cart
    Route::get('/cart', 'Web\CartController@index')->name('cart.index');
    Route::post('/cart/add-merch', 'Web\CartController@addMerchToCart')->name('cart.addMerch');
    Route::post('/cart/update/{cartItem}', 'Web\CartController@updateQuantity')->name('cart.update');
    Route::delete('/cart/{cartItem}', 'Web\CartController@destroy')->name('cart.destroy');

    // Auction History
    Route::get('/account/auction', [AuctionHistoryController::class, 'index'])
        ->name('account.auction_history');
});


// merch product route
Route::get('/merch/categories', 'Web\MerchProduct\GetMerchCategory')->name('merch.categories');
Route::get('/merch/{slug}', 'Web\MerchProduct\getDetail')->name('merch.products.detail');
Route::get('/merch-products/json', 'Web\MerchProduct\GetMerchProduct')->name('merch.products.json');

//midtrans-callback
Route::post('/payments/midtrans-notification','Account\PaymentCallbackController@receive');


Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    // ... route cart lainnya
    
    Route::get('/checkout', [CheckoutMerchController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutMerchController::class, 'process'])->name('checkout.process');
    
    Route::post('/checkout/pay', 'Web\PaymentController@payNow')->name('checkout.pay');
    Route::get('/checkout/success', function(){
        return "Pembayaran berhasil!";
    })->name('checkout.success');

    Route::get('/checkout/failed', function(){
        return "Pembayaran gagal!";
    })->name('checkout.failed');

    // ... route checkout lainnya
});

//Checkout
Route::post('/checkout/process', 'Web\CheckoutMerchController@process')->name('checkout.process');
Route::get('/checkout/success/{invoice}', 'Web\CheckoutMerchController@success')->name('checkout.success');
Route::post('/checkout/set-address', 'Web\CheckoutMerchController@setAddress')->name('checkout.set-address');
Route::post('/checkout/shipping-cost', 'Web\CheckoutMerchController@getShippingCost')->name('checkout.shipping-cost');


// API untuk fetch lokasi (dipakai AJAX di form)
Route::get('/get-kabupaten/{id}', function($id){
    return \App\Kabupaten::where('provinsi_id', $id)->get();
});

// List semua provinsi
Route::get('/lokasi/province', 'LocationController@province')->name('lokasi.province');

// List kabupaten berdasarkan provinsi
Route::get('/lokasi/city/{province_id}', 'LocationController@city')->name('lokasi.city');

// List kecamatan berdasarkan kabupaten
Route::get('/lokasi/district/{city_id}', 'LocationController@district')->name('lokasi.district');

Route::post('/alamat/store', 'UserAddressController@store')->name('alamat.store');
Route::get('/alamat/refresh', 'UserAddressController@refreshList')->name('alamat.refresh');
//Route::get('/cosuccess/{orderNumber}', 'Web\CheckoutMerchController@cosuccess')->name('cosuccess');


// Group Route Panduan
Route::prefix('panduan')->group(function () {
    
    // 1. Pembelian Produk
    Route::get('/pembelian', [PanduanController::class, 'pembelian'])
        ->name('panduan.beli');

    // 2. Peserta Lelang
    Route::get('/lelang-peserta', [PanduanController::class, 'lelangPeserta'])
        ->name('panduan.lelang.peserta');

    // 3. Penjualan Karya Lelang
    Route::get('/penjualan-karya', [PanduanController::class, 'penjualanKarya'])
        ->name('panduan.penjualan.karya');

    // 4. Penjualan Produk
    Route::get('/penjualan-produk', [PanduanController::class, 'penjualanProduk'])
        ->name('panduan.penjualan.produk');
        
});