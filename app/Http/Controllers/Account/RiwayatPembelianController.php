<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\OrderMerch;
use App\Order;
use Illuminate\Http\Request;
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
                           ->with('lelang.karya')
                           ->get();

            foreach ($lelangOrders as $order) {
                $order->order_type = 'lelang';
            }

            $orders = $merchOrders->concat($lelangOrders)->sortByDesc('created_at');

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
        
        return view('account.merch_history.history_detail', compact('order'));
    }

    public function showLelang($id)
    {
        $order = Order::with('lelang.karya')
                      ->where('id', $id)
                      ->where('user_id', Auth::user()->id)
                      ->firstOrFail();
        
        $order->order_type = 'lelang';

        // You might need a different detail view for auction orders
        // For now, let's reuse or create a generic one.
        // This assumes you have a route named 'account.lelang.order.show'
        // and a corresponding view.
        return view('account.lelang_history.history_detail', compact('order'));
    }
}
