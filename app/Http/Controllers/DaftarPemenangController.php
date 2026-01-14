<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Products;
use DB;

class DaftarPemenangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Ambil daftar pemenang dari kolom `winner_id` pada table `products`
        $daftarPemenang = Products::with([
            'winner',
            'imageUtama',
            'order',
            'bid' => function($q){
                $q->orderBy('id','desc')->limit(1);
            }
        ])
            ->whereNotNull('winner_id')
            ->where('status', 2)
            ->get();

        return view('admin.pemenang.index', compact('daftarPemenang'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        try {
            // $id is expected to be the product id; load product with winner, latest bid and order
            $detailBid = Products::with([
                'winner',
                'bid' => function($q){
                    $q->orderBy('id','desc')->limit(1);
                },
                'order'
            ])->findOrFail($id);
            $order = $detailBid->order ?? Order::where('product_id', $detailBid->id)->first();
            return view('admin.pemenang.show', compact('detailBid','order'));
        } catch (\Throwable $th) {
            abort(404);
        }
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
}
