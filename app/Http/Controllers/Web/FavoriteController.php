<?php

namespace App\Http\Controllers\Web;

use App\Favorite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $favorite = Favorite::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ]);
            return response()->json(['status' => 'added']);
        }
    }
}
