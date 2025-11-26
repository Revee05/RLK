<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use App\Services\CreateSnapTokenService;
use Illuminate\Support\Facades\Log;
use Auth; 
use Illuminate\Support\Str;
use App\Bid;
use App\OrderMerch;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check()) {
            //Tampilkan data sesuai id user dan status menunggu pembayaran
            $orders = Order::where('user_id',Auth::user()->id)->get();
            return view('account.orders.index', compact('orders'));
        }
        return abort(404);
    }

    /**
     * Display purchase history page with statistics for merchandise orders
     *
     * @return \Illuminate\Http\Response
     */
    public function purchaseHistory()
    {
        if (Auth::check()) {
            $orders = OrderMerch::where('user_id', Auth::user()->id)
                          ->with(['address', 'shipper'])
                          ->orderBy('created_at', 'desc')
                          ->get();
            return view('account.orders.purchase_history', compact('orders'));
        }
        return abort(404);
    }

    /**
     * Show merchandise order detail
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showMerchOrder($id)
    {
        $order = OrderMerch::with(['address.provinsi', 'address.kabupaten', 'address.kecamatan', 'shipper'])
                          ->where('id', $id)
                          ->where('user_id', Auth::user()->id)
                          ->firstOrFail();
        
        return view('account.orders.show_merch', compact('order'));
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
        // dd($request->all());
        // $this->validate($request, [
        //     'name'=>'required',
        //     'user_id'=>'required',
        //     'phone'=>'required',
        //     'label_address'=>'required',
        //     'provinsi_id'=>'required',
        //     'kabupaten_id'=>'required',
        //     'kecamatan_id'=>'required',
        //     'address'=>'required',
        //     'weight'=>'required',
        //     'shipper'=>'required',
        //     'products_id'=>'required',
        //     'courier'=>'required',
        // ],[
        //     'name.required'=>'Nama wajib di isi',
        //     'user_id.required'=>'wajib di isi',
        //     'phone.required'=>'Nomer hp wajib di isi',
        //     'address.required'=>'Alamat wajib di isi',
        //     'provinsi_id.required'=>'Provinsi wajib di isi',
        //     'kabupaten_id.required'=>'Kabupaten wajib di isi',
        //     'kecamatan_id.required'=>'Kecamtan wajib di isi',
        //     // 'desa_id.required'=>'Desa wajib di isi',
        //     // 'kodepos'=>'Kodepost wajib di isi',
        //     'label_address'=>'Label alamat wajib di isi',
        // ]);
        try {

            $kode = Order::max('order_invoice');
            $urutan = (int) substr($kode, 5, 5);
            $urutan++;
            $huruf = "INV";
            $kodeInvoice = $huruf . sprintf("%05s", $urutan);


            if (empty($request->total_tagihan)) {
                $total_tagihan = $request->bid_terakhir + $request->total_ongkir;
            } else {
                $total_tagihan = $request->total_tagihan;
            }
            $order = Order::create([
                'user_id'=>Auth::user()->id,
                'name'=>$request->name,
                'phone'=>$request->phone,
                'label_address'=>$request->label_address ?? 'rumah',
                'address'=>$request->address,
                'provinsi_id'=>$request->provinsi_id,
                'kabupaten_id'=>$request->kabupaten_id,
                'kecamatan_id'=>$request->kecamatan_id,
                'product_id'=>$request->product_id,
                'pengirim'=>$request->pengirim ?? 'jne',
                'jenis_ongkir'=>$request->jenis_ongkir,
                'bid_terakhir'=>$request->bid_terakhir,
                'total_ongkir'=>$request->total_ongkir ?? '10000',
                'asuransi_pengiriman'=>$request->asuransi_pengiriman ?? '0',
                'orderid_uuid'=>Str::uuid(),
                'order_invoice'=>$kodeInvoice,
                'total_tagihan'=>$total_tagihan,
                'payment_status'=>$request->payment_status ?? '1',
            ]);
            return redirect()->route('account.invoice',$order->orderid_uuid);
        } catch (Exception $e) {
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::user()->id) {
            return abort(403);
        }
        
        return view('account.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
    public function invoice($invoice)
    {
        $order = Order::where('orderid_uuid',$invoice)->first();
        $expired = $order->where('created_at','<',\Carbon\Carbon::parse('-24 hours'))->first();
        //Jika pembayaran expired
        if ($expired) {
            //Periksa bid terakhir
            $bid = Bid::where('product_id',$order->product->id)->orderBy('created_at', 'desc')->first();
            if ($bid) {
                //kemudian update harga produk dengan bid terakhir yang belum di bayar karna pembayaran kadaluarsa
                $bid->product->update(['price'=>$bid->price]);
                //reset bid yang telah di update
                $bid->where('product_id',$order->product->id)->delete();

                return $this->expired();
            } else {
                return $this->expired();
            }
        }
        $snapToken = $order->snap_token;
        if (empty($snapToken)) {
            // Jika snap token masih NULL, buat token snap dan simpan ke database
            try {
                
                $midtrans = new CreateSnapTokenService($order);
                $snapToken = $midtrans->getSnapToken();
     
                $order->snap_token = $snapToken;
                $order->save();
            
            } catch (Exception $e) {
                Log::error("ONGKRI".$e->getMessage()); 
            }
        }
 
        return view('account.checkout.order', compact('order', 'snapToken'));
    }
    public function finish(Request $request)
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        if ($orderId && $statusCode == '200') {
            
            $order = Order::where('orderid_uuid',$orderId)->first();
            if ($order) {
                $order->update(['payment_status'=>'2']);
                return view('account.checkout.finish');
            } else {
                return redirect()->route('account.invoice',$order->orderid_uuid);
            }
        } else {
            return redirect()->route('account.invoice',$order->orderid_uuid);
        }
        return redirect()->route('account.invoice',$order->orderid_uuid);
    }
    public function unfinish()
    {
        return view('account.checkout.unfinish');
    }
    public function error()
    {
        return view('account.checkout.eror');
    }
    public function expired()
    {
        return view('account.checkout.expired');
    }
    
    
    
}
