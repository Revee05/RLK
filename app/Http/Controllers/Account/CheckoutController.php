<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Products;
use App\UserAddress;
use Auth; 
use Kavist\RajaOngkir\RajaOngkir;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use DB; 
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Bid;
use App\Order;
class CheckoutController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all(),"disini");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order, $id)
    {
        
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
    public function cart($slug)
    {
        try {
            $permalink = \Crypt::decrypt($slug);
            $product = Products::where('slug',$permalink)->first();

            if (Order::where('product_id',$product->id)->first()) {
                $order = Order::where('product_id',$product->id)->first();
                return redirect()->route('account.invoice',$order->orderid_uuid);
            }
            $bid = Bid::where('product_id',$product->id)->orderBy('created_at', 'desc')->first();
            // if ($product->status == '1') {
                $provinsis = Cache::remember('provinsis', 180, function () {
                    return Provinsi::pluck('nama_provinsi','id');
                });
                $kabupatens = Cache::remember('kabupatens', 180, function () {
                    return Kabupaten::all();
                });
                $kecamatans = Cache::remember('kecamatans', 180, function () {
                    return  Kecamatan::all();
                });
         
                return view('account.checkout.checkout',compact('product','bid','provinsis','kabupatens','kecamatans'));
            // } else {
                // return abort(404);
            // }
        
        } catch (DecryptException $e) {
            Log::error("checkout".$e->getMessage()); 
            return abort(404);
        }
    }
    public function getOngkir(Request $request)
    {
        $this->validate($request, [
            'destination' => 'required',
            'weight' => 'required|integer'
        ]);
        try {
            
            $url = 'https://api.rajaongkir.com/starter/cost';
            $client = new Client();
            $response = $client->request('POST', $url, [
                'headers' => [
                    'key' => '3a87dd98103ee7000c2ba6a1b7b0d47c'
                ],
                'form_params' => [
                    'origin' => 398, //ASAL PENGIRIMAN, 398 = SEMARANG
                    // 'destination' => 80,
                    'destination' => $request->destination,
                    // 'weight' => 1300,
                    'weight' => $request->weight,
                    'courier' => 'jne' 
                    //MASUKKAN KEY KURIR LAINNYA JIKA INGIN MENDAPATKAN DATA ONGKIR DARI KURIR YANG LAIN
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            return $body;
        } catch (Exception $e) {
             Log::error("ONGKRI".$e->getMessage()); 
        }
    }
}
