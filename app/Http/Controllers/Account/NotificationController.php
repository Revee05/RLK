<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;
use App\Bid;
use Auth;

class NotificationController extends Controller
{
    public function checkWinner()
    {
        $user = Auth::user();

        $bid = Bid::where('user_id', $user->id)
            ->whereHas('product', function ($q) {
                $q->where('status', 2); 
            })
            ->latest()
            ->first();

        if (!$bid) {
            return response()->json(['winner' => false]);
        }

        return response()->json([
            'winner' => true,
            'product_title' => $bid->product->title,
            'checkout_url' => url('/account/checkout/' . \Crypt::encrypt($bid->product->slug)),
        ]);
    }

}
