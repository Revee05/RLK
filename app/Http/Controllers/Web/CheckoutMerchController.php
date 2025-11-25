<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserAddress;
use App\Shipper;
use App\OrderMerch;
use Illuminate\Support\Str;
use App\Province;
use App\City;
use App\District;
use App\Services\RajaOngkirService;


class CheckoutMerchController extends Controller
{
    public function index()
    {
        // Ambil cart dari session
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Keranjang masih kosong!');
        }

        // Hitung subtotal
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Hitung total barang
        $totalQty = collect($cart)->sum('quantity');

        // Ambil alamat user
        $addresses = UserAddress::where('user_id', auth()->id())->get();

        // Ambil shipper
        $shippers = Shipper::all();

        // Ambil semua provinsi
        $province = Province::all();

        return view('web.checkout.index', compact(
            'cart',
            'subtotal',
            'totalQty',
            'addresses',
            'shippers',
            'province'
        ));
    }

    public function process(Request $request)
    {
        $request->validate([
            'address_id'   => 'required',
            'shipper_id'   => 'required',
            'jenis_ongkir' => 'required',
            'total_ongkir' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Keranjang kosong.');
        }

        $totalBarang = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $totalTagihan = $totalBarang + $request->total_ongkir;

        // Simpan ke database
        $order = OrderMerch::create([
            'user_id'       => auth()->id(),
            'address_id'    => $request->address_id,
            'items'         => json_encode($cart), // simpan aman
            'shipper_id'    => $request->shipper_id,
            'jenis_ongkir'  => $request->jenis_ongkir,
            'total_ongkir'  => $request->total_ongkir,
            'total_tagihan' => $totalTagihan,
            'invoice'       => 'INV-' . strtoupper(Str::random(10)),
            'status'        => 'pending',
        ]);

        // Bersihkan cart
        session()->forget('cart');

        return redirect()->route('checkout.success', $order->invoice);
    }

    public function setAddress(Request $request)
    {
        $address = UserAddress::with(['province','city','district'])
                    ->find($request->address_id);

        if(!$address){
            return response()->json(['status'=>'error']);
        }

        session(['checkout_address' => $address]);

        return response()->json([
            'status' => 'success',
            'address' => [
                'label_address' => $address->label_address,
                'name'          => $address->name,
                'phone'         => $address->phone,
                'address'       => $address->address,
                'district'      => $address->district->name ?? '',
                'city'          => $address->city->name ?? '',
                'province'      => $address->province->name ?? '',
            ]
        ]);
    }

    public function calculateShipping(Request $request)
    {
        // Ambil semua kurir dari tabel shippers
        $shippers = Shipper::select('id', 'name')->get();

        $result = [];

        foreach ($shippers as $ship) {
            $result[] = [
                'id'    => $ship->id,
                'name'  => $ship->name,
                'price' => 10000,       // flat 0
                'eta'   => '-',     // default
            ];
        }

        return response()->json($result);
    }

    public function getShippingCost(Request $request)
    {
        try {
            $origin         = (int) $request->origin;
            $destination    = (int) $request->destination;
            $weight         = (int) $request->weight ?: 1000;

            $couriers = ['jne', 'tiki', 'pos'];

            \Log::info('PAYLOAD RAJAONGKIR:', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $couriers,
                'price' => 'lowest'
            ]);

            $rajaOngkir = new RajaOngkirService();
            $result = [];

            foreach ($couriers as $courier) {

                $response = $rajaOngkir->calculateCost(
                    $origin,
                    $destination,
                    $weight,
                    $courier,
                    'lowest'
                );

                if (!empty($response['data'])) {

                    foreach ($response['data'] as $service) {
                        $result[] = [
                            'id'    => $courier . '-' . $service['service'],
                            'name'  => strtoupper($courier) . ' - ' . $service['service'],
                            'price' => $service['cost'],
                            'eta'   => $service['etd']
                        ];
                    }
                }
            }

            usort($result, fn($a,$b) => $a['price'] <=> $b['price']);

            return response()->json($result);

        } catch (\Throwable $e) {
            \Log::error('Shipping cost error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal mengambil data kurir.'
            ], 500);
        }
    }



    public function success($invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();
        return view('web.checkout.success', compact('order'));
    }
}
