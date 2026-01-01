<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserAddress;
use App\Shipper;
use App\OrderMerch;
use App\CartItem;
use Illuminate\Support\Str;
use App\Province;
use App\City;
use App\District;
use Illuminate\Support\Facades\DB;
use App\Services\RajaOngkirService;
use App\models\MerchProductVariant;
use Xendit\Xendit;

class CheckoutMerchController extends Controller
{
    /**
     * Tampilan Halaman Checkout
     */
    public function index(Request $request)
    {
        if (!$request->has('cart_item_ids') || empty($request->input('cart_item_ids'))) {
            return redirect()->route('cart.index')
                ->with('error', 'Silakan pilih minimal satu produk untuk checkout!');
        }

        $cartItems = CartItem::with([
            'merchProduct',
            'merchVariant.images',
            'merchSize'
        ])
            ->where('user_id', auth()->id())
            ->whereIn('id', $request->input('cart_item_ids'))
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Item yang dipilih tidak valid atau sudah tidak tersedia.');
        }

        // Simpan pilihan ke session
        session([
            'checkout_selected_item_ids' => $request->input('cart_item_ids', []),
            'checkout_gift_wrap' => $request->has('wrap_product'),
        ]);

        $isGiftWrap = $request->has('wrap_product');
        $giftWrapPrice = $isGiftWrap ? 10000 : 0;

        $cart = $cartItems->map(function ($item) {
            $imagePath = '/img/default.png';

            if ($item->merchVariant && $item->merchVariant->images->isNotEmpty()) {
                $imagePath = $item->merchVariant->images->first()->image_path;
            } elseif ($item->merchProduct && $item->merchProduct->images->isNotEmpty()) {
                $imagePath = $item->merchProduct->images->first()->image_path;
            }

            $productName = $item->merchProduct->name ?? 'Unknown Product';

            return [
                'id'           => $item->id,
                'name'         => $productName,
                'price'        => $item->price,
                'quantity'     => $item->quantity,
                'image'        => $imagePath,
                'variant_name' => $item->merchVariant->name ?? null,
                'variant_code' => $item->merchVariant->code ?? null,
                'discount'     => $item->merchVariant->discount ?? null,
                'stock'        => $item->merchVariant->stock ?? null,
                'weight'       => $item->merchVariant->weight ?? null,
                'size_name'    => $item->merchSize->size ?? null,
                'product_id'   => $item->merch_product_id,
                'variant_id'   => $item->merch_product_variant_id,
                'size_id'      => $item->merch_product_variant_size_id,
            ];
        });

        $subtotalBarang = $cart->sum(fn($item) => $item['price'] * $item['quantity']);
        $totalQty = $cart->sum('quantity');
        $subtotal = $subtotalBarang;

        $selectedItemIds = $cartItems->pluck('id')->toArray();
        $addresses = UserAddress::where('user_id', auth()->id())->orderByDesc('is_primary')->orderBy('id')->get();
        $shippers = Shipper::all();
        $province = Province::all();

        $selectedAddressId = session('checkout_address_id');
        $selectedAddress = $selectedAddressId
            ? UserAddress::with(['province', 'city', 'district'])->find($selectedAddressId)
            : auth()->user()->userAddress()->with(['province', 'city', 'district'])->first();

        return view('web.checkout.index', compact(
            'cart',
            'subtotal',
            'subtotalBarang',
            'giftWrapPrice',
            'totalQty',
            'addresses',
            'shippers',
            'province',
            'selectedItemIds',
            'isGiftWrap',
            'selectedAddress',
        ));
    }

    /**
     * Proses Pembuatan Order
     */
    public function process(Request $request)
    {
        \Log::info('Checkout PROCESS: mulai proses checkout', $request->all());

        $request->validate([
            'address_id'        => 'required',
            'shipping_method'   => 'required',
            'selected_item_ids' => 'required',
        ]);

        $shippingMethod = $request->shipping_method;
        $selectedIds = session('checkout_selected_item_ids', []);

        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'Data item tidak valid.');
        }

        $cartItems = CartItem::with(['merchProduct', 'merchVariant.images', 'merchSize'])
                    ->where('user_id', auth()->id())
                    ->whereIn('id', $selectedIds)
                    ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $orderItems = $cartItems->map(function ($item) {
            $imagePath = '/img/default.png';
            if ($item->merchVariant && $item->merchVariant->images->isNotEmpty()) {
                $imagePath = $item->merchVariant->images->first()->image_path;
            }

            return [
                'id'           => $item->id,
                'name'         => $item->merchProduct->name ?? 'Unknown Product',
                'price'        => $item->price,
                'qty'          => $item->quantity,
                'image'        => $imagePath,
                'variant_name' => $item->merchVariant->name ?? null,
                'size_name'    => $item->merchSize->size ?? null,
            ];
        });

        $totalBarang = $cartItems->sum(fn($item) => $item->price * $item->quantity);
        $totalOngkir = $shippingMethod === 'delivery' ? (int) $request->input('total_ongkir', 0) : 0;
        
        $giftWrap = session('checkout_gift_wrap', false);
        $biayaGiftWrap = $giftWrap ? 10000 : 0;

        if ($shippingMethod === 'delivery') {
            $shippingData = [
                'type'    => 'delivery',
                'name'    => $request->shipping_name,
                'code'    => $request->shipping_code,
                'service' => $request->shipping_service,
                'cost'    => $totalOngkir,
                'etd'     => $request->shipping_etd,
            ];
        } else {
            $shippingData = [
                'type' => 'pickup',
                'name' => 'Ambil di Toko',
                'cost' => 0,
                'etd'  => null,
            ];
        }

        $totalTagihan = $totalBarang + $totalOngkir + $biayaGiftWrap;

        try {
            DB::beginTransaction();

            $order = OrderMerch::create([
                'user_id'       => auth()->id(),
                'address_id'    => $request->address_id,
                'items'         => $orderItems->toJson(), 
                'shipping'      => json_encode($shippingData),
                'gift_wrap'     => $giftWrap,
                'jenis_ongkir'  => $request->jenis_ongkir ?? 'Regular', 
                'total_ongkir'  => $totalOngkir,
                'total_tagihan' => $totalTagihan,
                'invoice'       => 'INV-' . strtoupper(Str::random(10)),
                'status'        => 'pending',
                'note'          => $request->note ?? '',
            ]);

            CartItem::whereIn('id', $selectedIds)->delete();

            DB::commit();

            session()->forget(['checkout_selected_item_ids', 'checkout_gift_wrap']);
           
            return redirect()->route('checkout.preview', ['invoice' => $order->invoice]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout PROCESS Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses pesanan.');
        }
    }

    public function setAddress(Request $request)
    {
        $address = UserAddress::with(['province', 'city', 'district'])->find($request->address_id);

        if (!$address) {
            return response()->json(['status' => 'error']);
        }

        session(['checkout_address_id' => $address->id]);

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
                'district_id'   => $address->district_id,
                'city_id'       => $address->city_id,
                'province_id'   => $address->province_id,
            ]
        ]);
    }

    public function getShippingCost(Request $request)
    {
        if (!$request->filled('origin') || !$request->filled('destination')) {
            return response()->json(['error' => 'Alamat pengiriman belum lengkap.'], 422);
        }

        try {
            $origin      = (int) $request->origin;
            $destination = (int) $request->destination;

            $cartItems = CartItem::with('merchVariant')
                        ->where('user_id', auth()->id())
                        ->whereIn('id', $request->selected_item_ids ?? [])
                        ->get();

            $weight = $cartItems->sum(fn($item) => ($item->merchVariant->weight ?? 0) * $item->quantity);
            $weight = $weight > 0 ? $weight : 1000;

            $couriers = ['jne', 'tiki', 'pos'];
            $result   = [];

            $rajaOngkir = new RajaOngkirService();

            foreach ($couriers as $courier) {
                $response = $rajaOngkir->calculateCost($origin, $destination, $weight, $courier, 'lowest');

                if (empty($response['data'])) continue;

                foreach ($response['data'] as $service) {
                    $result[] = [
                        'id'    => $courier . '-' . $service['service'],
                        'name'  => strtoupper($courier) . ' - ' . $service['service'],
                        'price' => (int) $service['cost'],
                        'eta'   => $service['etd'] ?? '-',
                    ];
                }
            }

            usort($result, fn ($a, $b) => $a['price'] <=> $b['price']);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal mengambil data kurir.'], 500);
        }
    }

    public function preview($invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $items = json_decode($order->items, true);
        $shipping = json_decode($order->shipping, true);
        $giftWrap = (bool) $order->gift_wrap;
        $giftWrapCost = $giftWrap ? 10000 : 0;
        
        $subtotal = collect($items)->sum(fn($item) => $item['price'] * $item['qty']);
        $shippingCost = $order->total_ongkir ?? 0;
        $isPickup = ($shipping['type'] ?? '') === 'pickup';

        return view('web.checkout.preview', compact('order', 'shippingCost', 'items', 'shipping', 'giftWrap', 'giftWrapCost', 'isPickup', 'subtotal'));
    }

    public function success($invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();
        return view('web.checkout.success', compact('order'));
    }
}