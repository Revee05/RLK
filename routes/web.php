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
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

require_once  __DIR__ . "/admin.php";
require_once  __DIR__ . "/account.php";

Auth::routes();
// sample routes
Route::get('/cart', function () {
    return view('web.cart');
});

Route::get('/test-add-cart', function() {
    session()->put('cart', [
        [
            'product_id' => 1,
            'name' => 'Merch Hoodie',
            'price' => 150000,
            'quantity' => 2,
        ],
        [
            'product_id' => 2,
            'name' => 'Sticker Set',
            'price' => 25000,
            'quantity' => 1,
        ]
    ]);

    return 'Cart ditambahkan!';
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
Route::group(['middleware' => ['auth']], function () {
    // Halaman List Keranjang
    Route::get('/cart', 'Web\CartController@index')->name('cart.index');

    // Tambah ke Keranjang (Merch)
    // NOTE: Parameter {id} dihapus karena data dikirim via form (Request body)
    Route::post('/cart/add-merch', 'Web\CartController@addMerchToCart')->name('cart.addMerch');

    // Update Quantity (AJAX)
    // Menggunakan Model Binding {cartItem} sesuai controller
    Route::post('/cart/update/{cartItem}', 'Web\CartController@updateQuantity')->name('cart.update');

    // Hapus Item
    Route::delete('/cart/{cartItem}', 'Web\CartController@destroy')->name('cart.destroy');
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
    // ... route checkout lainnya
});

//Checkout
Route::post('/checkout/process', 'Web\CheckoutMerchController@process')->name('checkout.process');
Route::get('/checkout/success/{invoice}', 'Web\CheckoutMerchController@success')->name('checkout.success');
Route::post('/checkout/set-address', 'Web\CheckoutMerchController@setAddress')->name('checkout.set-address');

// API untuk fetch lokasi (dipakai AJAX di form)
Route::get('/get-kabupaten/{id}', function($id){
    return \App\Kabupaten::where('provinsi_id', $id)->get();
});

// List semua provinsi
Route::get('/lokasi/provinsi', 'ProvinsiController@getAll')->name('lokasi.provinsi');

// List kabupaten berdasarkan provinsi
Route::get('/lokasi/kabupaten/{provinsi_id}', 'KabupatenController@getByProvinsi')->name('lokasi.kabupaten');

// List kecamatan berdasarkan kabupaten
Route::get('/lokasi/kecamatan/{kabupaten_id}', 'KecamatanController@getByKabupaten')->name('lokasi.kecamatan');

Route::post('/alamat/store', 'UserAddressController@store')->name('alamat.store');
Route::get('/alamat/refresh', 'UserAddressController@refreshList')->name('alamat.refresh');
Route::post('/checkout/shipping-cost', 'Web\CheckoutMerchController@calculateShipping')->name('checkout.shipping-cost');

Route::post('/favorite/toggle', [\App\Http\Controllers\Web\FavoriteController::class, 'toggle'])
    ->name('favorite.toggle');


   Route::get('/account/favorites', [UsersController::class, 'favorites'])->name('account.favorites');

   Route::delete('/account/favorites/{id}', 'Account\FavoriteController@remove')
    ->name('account.favorites.remove');
