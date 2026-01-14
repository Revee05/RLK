<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\OrderMerch;

class TransaksiController extends Controller
{
    /**
     * Display a listing of orders and orders_merch.
     */
    public function index(Request $request)
    {
        $orders = Order::with('user')->orderBy('id', 'desc')->take(50)->get();
        $ordersMerch = OrderMerch::with('user')->orderBy('id', 'desc')->take(50)->get();

        return view('admin.transaksi.index', compact('orders', 'ordersMerch'));
    }

    /**
     * Display the specified order or orderMerch.
     *
     * @param  mixed $id
     * @param  string|null $type  Optional: 'merch' to force OrderMerch lookup
     */
    public function show($id, $type = null)
    {
        // If caller specifies type=merch, look up in OrderMerch first
        if ($type === 'merch') {
            $data = OrderMerch::with('user','address','shipper')->findOrFail($id);
            return view('admin.transaksi.show', ['data' => $data, 'type' => 'merch']);
        }

        // Try to find in orders first
        $order = Order::with('user','address','shipper')->find($id);
        if ($order) {
            return view('admin.transaksi.show', ['order' => $order, 'type' => 'order']);
        }

        // Fallback: try OrderMerch
        $data = OrderMerch::with('user','address','shipper')->findOrFail($id);
        return view('admin.transaksi.show', ['data' => $data, 'type' => 'merch']);
    }
}
