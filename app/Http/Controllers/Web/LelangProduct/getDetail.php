<?php

namespace App\Http\Controllers\Web\LelangProduct;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;
use App\Bid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;

class getDetail extends Controller
{
    /**
     * Menampilkan detail produk lelang
     */
    public function show($slug)
    {
        // Validasi slug
        $validator = Validator::make(['slug'=>$slug], [
            'slug'=>['required','exists:products,slug']
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        try {
            $product = Products::where('slug', $slug)->first();
            if (empty($product)) {
                abort(404);
            }

            if (Auth::check() === false) {
                $bid = Bid::with('user')->where('product_id', $product->id)->get();
                $bids = $bid->map(function($data){
                    return [
                        'user' => $data->user,
                        'message' => $data->price,
                        'produk' => $data->product_id
                    ];
                });
                return view('web.detail', compact('product', 'bids'));
            }
            return view('web.detail', compact('product'));

        } catch (Exception $e) {
            Log::error('Detail Lelang Product :'. $e->getMessage());
            abort(500);
        }
    }
}