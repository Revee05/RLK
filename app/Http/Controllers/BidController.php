<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\Products;
use App\Events\MessageSent;
use App\Events\BidSent;
use Auth;

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

    // POST /bid/messages
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'message' => 'required',
            'produk' => 'required|integer',
            'tanggal' => 'required'
        ]);

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

    public function sendMessage(Request $request)
    {
        $price = $request->message;
        $productID = $request->produk;

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
