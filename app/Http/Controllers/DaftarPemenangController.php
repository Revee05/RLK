<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bid;
use App\Order;
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
        $daftarPemenang = Bid::whereHas('product', function($q){
           $q->where('status',2);
        })->whereRaw('id in (select max(id) from bid group by (product_id))')->get();
        // ->groupBy('product_id')->select('product_id','id', DB::raw('count(*) as total'))->get();
        // dd($daftarPemenang);
        return view('admin.pemenang.index',compact('daftarPemenang'));
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
            $detailBid = Bid::findOrfail($id);
            $order = Order::where('product_id',$detailBid->product_id)->first();
            return view('admin.pemenang.show',compact('detailBid','order'));
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
