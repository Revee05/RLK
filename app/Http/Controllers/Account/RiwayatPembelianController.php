<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\OrderMerch;
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
            $orders = OrderMerch::where('user_id', Auth::user()->id)
                          ->with(['address', 'shipper'])
                          ->orderBy('created_at', 'desc')
                          ->get();
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
        
        return view('account.merch_history.history_detail', compact('order'));
    }
}
