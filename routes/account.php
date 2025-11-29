<?php

/*
|--------------------------------------------------------------------------
| Account Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['verify' => true]);
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');

Route::group(['prefix' => '/account', 'middleware' => ['auth', 'verified', 'IsMember']], function () {
    Route::get('/profile', 'Account\MemberController@profile')->name('account.dashboard');
    Route::post('/profile', 'Account\MemberController@updateProfile')->name('update.profil');
    // upload avatar separately
    Route::post('/profile/avatar', 'Account\MemberController@uploadAvatar')->name('account.avatar.upload');
    Route::resource('/address', 'Account\AddressController', ['as' => 'account'])->except(['create']);
    Route::post('/checkout/get/ongkir', 'Account\CheckoutController@getOngkir')->name('checkout.get.ongkir');
    Route::get('/checkout/{slug}', 'Account\CheckoutController@cart')->name('checkout.cart');
    Route::resource('/checkout', 'Account\CheckoutController', ['as' => 'account']);
    Route::get('/address/get/desa/{id}', 'Account\AddressController@getDesa')->name('address.get.desa');
    Route::get('/ubah/kata-sandi', 'Account\MemberController@kataSandi')->name('account.katasandi');
    Route::resource('orders', 'Account\OrderController', ['as' => 'account']);
    Route::get('/riwayat-pembelian', 'Account\OrderController@purchaseHistory')->name('account.purchase.history');
    Route::get('/riwayat-pembelian/merch-order/{id}', 'Account\OrderController@showMerchOrder')->name('account.merch.order.show');
    Route::get('invoice/payment/finish', 'Account\OrderController@finish')->name('account.invoice.finish');
    Route::get('invoice/payment/unfinish', 'Account\OrderController@unfinish')->name('account.invoice.unfinish');
    Route::get('invoice/payment/error', 'Account\OrderController@error')->name('account.invoice.error');
    Route::get('invoice/payment/expired', 'Account\OrderController@expired')->name('account.invoice.expired');
    Route::get('invoice/{invoice}', 'Account\OrderController@invoice')->name('account.invoice');
    // Notification settings
    Route::get('/notifications', 'Account\NotificationController@index')->name('account.notifications');
    Route::post('/notifications', 'Account\NotificationController@update')->name('account.notifications.update');
});
