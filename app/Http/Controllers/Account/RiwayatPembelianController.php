<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\OrderMerch;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Auth;

class RiwayatPembelianController extends Controller
{
    /**
     * Display purchase history page with statistics for merchandise orders
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            $merchOrders = OrderMerch::where('user_id', Auth::user()->id)
                          ->with(['address.province', 'address.city', 'address.district', 'shipper'])
                          ->orderBy('created_at', 'desc')
                          ->get();
            
            foreach ($merchOrders as $order) {
                $order->order_type = 'merch';
            }

            $lelangOrders = Order::where('user_id', Auth::user()->id)
                           ->with(['product', 'product.karya', 'product.kategori', 'product.images', 'bid.user'])
                           ->orderBy('created_at', 'desc')
                           ->get();

            if (app()->environment(['local', 'testing', 'development'])) {
                Log::info('DEBUG Lelang Orders Raw Data', [
                    'count' => $lelangOrders->count(),
                    'sample_data' => $lelangOrders->first() ? [
                        'id' => $lelangOrders->first()->id,
                        'order_invoice' => $lelangOrders->first()->order_invoice,
                        'orderid_uuid' => $lelangOrders->first()->orderid_uuid,
                        'payment_status' => $lelangOrders->first()->payment_status,
                        'product_id' => $lelangOrders->first()->product_id,
                        'product' => $lelangOrders->first()->product ? 'exists' : 'null',
                        'product_title' => $lelangOrders->first()->product ? $lelangOrders->first()->product->title : 'null',
                        'karya' => $lelangOrders->first()->product && $lelangOrders->first()->product->karya ? 'exists' : 'null',
                    ] : 'no data',
                ]);
            }

            foreach ($lelangOrders as $order) {
                // Samakan struktur data dengan OrderMerch untuk digunakan di view riwayat pembelian
                $order->order_type = 'lelang';

                // Invoice: untuk lelang disimpan di kolom order_invoice
                $order->invoice = $order->order_invoice;

                // Safety fallbacks: some orders may reference a deleted/missing product
                if (!$order->product) {
                    $order->product_exists = false;
                    $order->product_title = $order->product_title ?? 'Produk tidak tersedia';
                    $order->product_image = 'assets/img/default.jpg';
                } else {
                    $order->product_exists = true;
                    $order->product_title = $order->product->title;
                    $order->product_image = optional($order->product->imageUtama)->path ?? 'assets/img/default.jpg';
                }

                // Status UI: mapping dari payment_status numerik ke string yang dipakai di view
                // 1: pending, 2: success, 3: expired, 4: cancelled
                switch ((int) $order->payment_status) {
                    case 2:
                        $order->status = 'success';
                        break;
                    case 3:
                        $order->status = 'expired';
                        break;
                    case 4:
                        $order->status = 'cancelled';
                        break;
                    case 1:
                    default:
                        $order->status = 'pending';
                        break;
                }
            }

            $orders = $merchOrders->concat($lelangOrders)->sortByDesc('created_at');

            if (app()->environment(['local', 'testing', 'development'])) {
                Log::info('RiwayatPembelianController@index', [
                    'user_id' => Auth::user()->id,
                    'merch_count' => $merchOrders->count(),
                    'lelang_count' => $lelangOrders->count(),
                    'orders_count' => $orders->count(),
                    'order_ids' => $orders->pluck('id')->values()->all(),
                ]);
            }

            return view('account.merch_history.purchase_history', compact('orders'));
        }
        return abort(404);
    }

    /**
     * Show merchandise order detail
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = OrderMerch::with(['address.province', 'address.city', 'address.district', 'shipper'])
                          ->where('id', $id)
                          ->where('user_id', Auth::user()->id)
                          ->firstOrFail();
        
        $order->order_type = 'merch';

        if (app()->environment(['local', 'testing', 'development'])) {
            Log::info('RiwayatPembelianController@show', [
                'user_id' => Auth::user()->id,
                'order_id' => $order->id,
                'order_type' => 'merch',
            ]);
        }

        return view('account.merch_history.history_detail', compact('order'));
    }

    public function showLelang($id)
    {
        try {
            $order = Order::with(['product.karya', 'product.kategori', 'product.images', 'bid.user', 'provinsi', 'kabupaten', 'kecamatan'])
                          ->where('id', $id)
                          ->where('user_id', Auth::user()->id)
                          ->firstOrFail();
            
            $order->order_type = 'lelang';

            // Samakan properti yang dipakai di Blade dengan OrderMerch
            $order->invoice = $order->order_invoice;

            // Safety fallbacks: some orders may reference a deleted/missing product
            if (!$order->product) {
                $order->product_exists = false;
                $order->product_title = 'Produk tidak tersedia';
                $order->product_image = 'assets/img/default.jpg';
            } else {
                $order->product_exists = true;
                $order->product_title = $order->product->title ?? 'Produk Lelang';
                $order->product_image = optional($order->product->imageUtama)->path ?? 'assets/img/default.jpg';
            }

            switch ((int) $order->payment_status) {
                case 2:
                    $order->status = 'success';
                    break;
                case 3:
                    $order->status = 'expired';
                    break;
                case 4:
                    $order->status = 'cancelled';
                    break;
                case 1:
                default:
                    $order->status = 'pending';
                    break;
            }

            if (app()->environment(['local', 'testing', 'development'])) {
                Log::info('RiwayatPembelianController@showLelang', [
                    'user_id' => Auth::user()->id,
                    'order_id' => $order->id,
                    'order_type' => 'lelang',
                    'product_id' => $order->product_id,
                ]);
            }

            return view('account.lelang_history.history_detail', compact('order'));
        } catch (\Exception $e) {
            if (app()->environment(['local', 'testing', 'development'])) {
                Log::error('RiwayatPembelianController@showLelang Error', [
                    'user_id' => Auth::user()->id,
                    'order_id' => $id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            throw $e;
        }
    }
}
