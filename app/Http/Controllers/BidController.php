<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\Products;
use App\Events\MessageSent;
use App\Events\BidSent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // WAJIB: Import Carbon untuk cek waktu

class BidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // GET /bid/messages/{slug}
    public function messages($slug)
    {
        $product = Products::where('slug', $slug)->with(['bids.user'])->firstOrFail();
        $bids = $product->bids()->with('user')->orderBy('created_at','asc')->get();

        // Map to array => { user: {id,name}, message: price, tanggal }
        $payload = $bids->map(function($b){
            return [
                'user' => ['id' => $b->user->id, 'name' => $b->user->name],
                'message' => $b->price,
                'tanggal' => $b->created_at->toDateTimeString()
            ];
        });

        return response()->json($payload);
    }

    // POST /bid/messages (Main Endpoint untuk Bid)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required',
            'produk' => 'required|integer',
            'tanggal' => 'required'
        ]);

        // --- [VALIDASI KEAMANAN DIMULAI] ---

        $product = Products::find($request->produk);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
        }

        // 1. Cek Status Database (Menangkap jika Robot Scheduler sudah bekerja)
        if ($product->status != 1) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Maaf, lelang ini sudah ditutup atau terjual!'
            ], 400);
        }

        // 2. Cek Jam Dinding / Realtime (Menangkap gap waktu sebelum Robot bekerja)
        // Ini mencegah user ngebid di detik ke 59 tapi request baru masuk di detik 01 lewat.
        if (Carbon::now() > $product->end_date) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Waktu lelang SUDAH HABIS!'
            ], 400);
        }

        // --- [VALIDASI KEAMANAN SELESAI] ---


        // store bid
        $bid = new Bid();
        $bid->user_id = $request->user_id;
        $bid->product_id = $request->produk;
        $bid->price = $request->message;
        $bid->save();

        // get user instance to broadcast
        $user = Auth::user();
        if(!$user){
            // fallback: find user by id
            $user = \App\Models\User::find($request->user_id);
        }

        // Broadcast MessageSent and BidSent on product.{id}
        broadcast(new MessageSent($user, $bid->price, $request->tanggal, $request->produk));
        broadcast(new BidSent($bid->price, $request->produk));

        return response()->json([
            'status' => 'ok',
            'bid' => $bid
        ]);
    }

    // Endpoint Alternatif (Chat/Direct Message Bid)
    public function sendMessage(Request $request)
    {
        $price = $request->message;
        $productID = $request->produk;

        // --- [VALIDASI KEAMANAN TAMBAHAN] ---
        // Kita juga harus mengamankan pintu ini
        $product = Products::find($productID);

        // Jika produk tidak ada, status bukan 1, atau waktu habis -> TOLAK
        if (!$product || $product->status != 1 || Carbon::now() > $product->end_date) {
             return ['status' => 'Lelang sudah ditutup / Waktu Habis'];
        }
        // --- [SELESAI VALIDASI] ---

        $user = Auth::user();

        $exists = Bid::where('product_id', $productID)
                    ->where('price', $price)
                    ->first();

        if ($exists) {
            return ['status' => 'Bid exists'];
        }

        $save = $user->bid()->create([
            'price' => $price,
            'product_id' => $productID
        ]);

        broadcast(new MessageSent($user, $price, $save->created_at))->toOthers();
        broadcast(new BidSent($price))->toOthers();

        return ['status' => 'OK'];
    }

}