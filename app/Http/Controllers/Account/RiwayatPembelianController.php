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
                          ->with(['address', 'shipper'])
                          ->get();
            
            foreach ($merchOrders as $order) {
                $order->order_type = 'merch';
            }

            $lelangOrders = Order::where('user_id', Auth::user()->id)
                           ->with('product.karya')
                           ->get();

            foreach ($lelangOrders as $order) {
                $order->order_type = 'lelang';
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
            $order = Order::with('product.karya')
                          ->where('id', $id)
                          ->where('user_id', Auth::user()->id)
                          ->firstOrFail();
            
            $order->order_type = 'lelang';

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
