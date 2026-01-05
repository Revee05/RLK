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
    // ==================================================================
    // METHOD INDEX: Menggunakan Logika Anda (Database & Checkbox)
    // ==================================================================
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
            ->whereIn('id', $request->input('cart_item_ids')) // <--- FIX: Wajib filter ID
            ->get();

        // Validasi: Jika data tidak ditemukan di DB (misal ID dimanipulasi/dihapus)
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Item yang dipilih tidak valid atau sudah tidak tersedia.');
        }

        // Simpan pilihan ke session
        session([
            'checkout_selected_item_ids' => $request->input('cart_item_ids', []),
            'checkout_gift_wrap' => $request->has('wrap_product'),
        ]);

        // Cek apakah user mencentang "Bungkus Kado"
        $isGiftWrap = $request->has('wrap_product');
        $giftWrapPrice = $isGiftWrap ? 10000 : 0;

        // Mapping Data untuk View
        $cart = $cartItems->map(function ($item) {
            // Logika Gambar
            $imagePath = '/img/default.png';

            if ($item->merchVariant && $item->merchVariant->images->isNotEmpty()) {
                $imagePath = $item->merchVariant->images->first()->image_path;
            } elseif ($item->merchProduct && $item->merchProduct->images->isNotEmpty()) {
                $imagePath = $item->merchProduct->images->first()->image_path;
            }

            // Logika Nama Produk
            $productName = $item->merchProduct->name ?? 'Unknown Product';

            return [
                'id'       => $item->id,
                'name'     => $productName,
                'price'    => $item->price,
                'quantity' => $item->quantity,
                'image'    => $imagePath,

                // --- Informasi Varian ---
                'variant_name' => $item->merchVariant->name ?? null,
                'variant_code' => $item->merchVariant->code ?? null,
                'discount'     => $item->merchVariant->discount ?? null,
                'stock'        => $item->merchVariant->stock ?? null,
                'weight'        => $item->merchVariant->weight ?? null,

                // Size
                'size_name'    => $item->merchSize->size ?? null,

                // Data ID Asli untuk proses backend nanti
                'product_id' => $item->merch_product_id,
                'variant_id' => $item->merch_product_variant_id,
                'size_id'    => $item->merch_product_variant_size_id,
            ];
        });

        // ==================================================================
        // 4. HITUNG TOTAL
        // ==================================================================
        $subtotalBarang = $cart->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $totalQty = $cart->sum('quantity');
        $subtotal = $subtotalBarang;

        // Simpan ID item yang terpilih untuk dikirim ke view (Input Hidden)
        $selectedItemIds = $cartItems->pluck('id')->toArray();

        // ==================================================================
        // 5. DATA PENDUKUNG (Alamat, Kurir, Provinsi)
        // ==================================================================
        $addresses = UserAddress::where('user_id', auth()->id())->orderByDesc('is_primary')
            ->orderBy('id')->get();

        // Ambil shipper
        $shippers = Shipper::all();

        // Ambil semua provinsi
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

    // ==================================================================
    // METHOD PROCESS
    // ==================================================================
    public function process(Request $request)
    {
        \Log::info('Checkout PROCESS: mulai proses checkout', $request->all());

        $request->validate([
            'address_id'        => 'required',
            'shipping_method'   => 'required',
            'selected_item_ids' => 'required',
        ]);

        $shippingMethod = $request->shipping_method; // delivery | pickup

        // Decode ID Item yang dipilih dari View
        
        $selectedIds = session('checkout_selected_item_ids', []);

        if (empty($selectedIds)) {
            \Log::warning('Checkout PROCESS: selected_item_ids kosong');
            return redirect()->route('cart.index')->with('error', 'Data item tidak valid.');
        }

        // Ambil Item dari Database (HANYA YANG DIPILIH)
        $cartItems = CartItem::with(['merchProduct', 'merchVariant.images', 'merchSize'])
                    ->where('user_id', auth()->id())
                    ->whereIn('id', $selectedIds) // <--- Ini perbaikan kuncinya
                    ->get();

        if ($cartItems->isEmpty()) {
            \Log::warning('Checkout PROCESS: item cart tidak ditemukan di DB');
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong atau item tidak ditemukan.');
        }

        // Mapping item supaya bisa ditampilkan di preview
        $orderItems = $cartItems->map(function ($item) {
            $imagePath = '/img/default.png';
            if ($item->merchVariant && $item->merchVariant->images->isNotEmpty()) {
                $imagePath = $item->merchVariant->images->first()->image_path;
            } elseif ($item->merchProduct && $item->merchProduct->images->isNotEmpty()) {
                $imagePath = $item->merchProduct->images->first()->image_path;
            }

            return [
                'id'           => $item->id,
                'name'         => $item->merchProduct->name ?? 'Unknown Product',
                'price'        => $item->price,
                'qty'          => $item->quantity,
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

        // Hitung Total Barang
        $totalBarang = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Total berat berdasarkan varian × quantity
        $totalWeight = $cartItems->sum(fn($item) => ($item->merchVariant->weight ?? 0) * $item->quantity);
        $totalWeight = $totalWeight > 0 ? $totalWeight : 1000;

        $shipperId = null;

        // Gift wrap
        $giftWrap = session('checkout_gift_wrap', false);
        $biayaGiftWrap = $giftWrap ? 10000 : 0;

        $totalOngkir = 0;
        $shippingData = [];

        if ($shippingMethod === 'delivery') {
            $request->validate([
                'total_ongkir'     => 'required|numeric|min:1',
                'shipping_name'    => 'required',
                'shipping_code'    => 'required',
                'shipping_service' => 'required',
            ]);

            $totalOngkir = (int) $request->input('total_ongkir', 0);

            // Ambil kode kurir dari request (misal 'jnt')
            $shipperCode = $request->shipping_code;

            // Cari ID shipper di tabel shippers
            $shipper = Shipper::where('code', $shipperCode)->first();
            $shipperId = $shipper ? $shipper->id : null;
    
            $shippingData = [
                'type'    => 'delivery',
                'name'    => $request->shipping_name,
                'code'    => $request->shipping_code,
                'service' => $request->shipping_service,
                'description' => $request->shipping_description ?? null,
                'cost'    => $totalOngkir,
                'etd'     => $request->shipping_etd,
            ];
        } else {
            // PICKUP
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
                
                'shipper_id'    => $shipperId,
                'total_ongkir'  => $totalOngkir,
                'total_tagihan' => $totalTagihan,
                'invoice'       => 'INV-' . strtoupper(Str::random(10)),
                'status'        => 'pending',
                'note'          => $request->note ?? '',
            ]);

            // Hapus HANYA Item yang diproses dari Database Cart
            CartItem::whereIn('id', $selectedIds)->delete();

            DB::commit();

            session()->forget([
                'checkout_selected_item_ids',
                'checkout_gift_wrap',
            ]);
           
            \Log::info('Checkout PROCESS: order berhasil disimpan', [
                'order_id' => $order->id,
                'invoice'  => $order->invoice,
                'user_id'  => auth()->id(),
            ]);

            return redirect()->route('checkout.preview', ['invoice' => $order->invoice]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout PROCESS: gagal menyimpan order', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'stack' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    public function setAddress(Request $request)
    {
        $address = UserAddress::with(['province', 'city', 'district'])
            ->find($request->address_id);

        if (!$address) {
            return response()->json(['status' => 'error']);
        }

        if ($address->user_id !== auth()->id()) {
            return response()->json(['status' => 'error'], 403);
        }

        // Simpan pilihan alamat ke session (agar persist saat refresh)
        session(['checkout_address_id' => $address->id]);

        return response()->json([
            'status' => 'success',
            'address' => [
                'label_address' => $address->label_address,
                'name'          => $address->name,
                'phone'         => $address->phone,
                'address'       => $address->address,
                'is_primary'    => $address->is_primary,
                'district'      => $address->district->name ?? '',
                'city'          => $address->city->name ?? '',
                'province'      => $address->province->name ?? '',

                // Tambahkan ID untuk kurir
                'district_id'   => $address->district_id,
                'city_id'       => $address->city_id,
                'province_id'   => $address->province_id,
            ]
        ]);
    }

    public function calculateShipping(Request $request)
    {
        // Ambil semua kurir dari tabel shippers
        $shippers = Shipper::all();
        $result = [];

        foreach ($shippers as $ship) {
            $result[] = [
                'id'    => $ship->id,
                'name'  => $ship->name,
                'code'  => $ship->code,
                'price' => 0,       // flat 0
                'eta'   => '-',     // default
            ];
        }
        return response()->json($result);
    }

    public function getShippingCost(Request $request)
    {
        \Log::info('CHECKOUT SHIPPING COST REQUEST', [
            'request_all' => $request->all(),
        ]);

        // VALIDASI ALAMAT
        if (!$request->filled('origin') || !$request->filled('destination')) {
            \Log::warning('ONGKIR GAGAL: ORIGIN / DESTINATION KOSONG', [
                'origin' => $request->origin,
                'destination' => $request->destination,
            ]);

            return response()->json([
                'error' => 'Alamat pengiriman belum lengkap.'
            ], 422);
        }

        try {
            $origin      = (int) $request->origin;
            $destination = (int) $request->destination;

            // Ambil cart item untuk menghitung total berat
            $cartItems = CartItem::with('merchVariant')
                        ->where('user_id', auth()->id())
                        ->whereIn('id', $request->selected_item_ids ?? [])
                        ->get();

            $weight = $cartItems->sum(fn($item) => ($item->merchVariant->weight ?? 0) * $item->quantity);
            $weight = $weight > 0 ? $weight : 1000;

            // Ambil semua kode kurir dari tabel shippers
            $couriers = Shipper::pluck('code')->toArray();
            $result   = [];

            \Log::info('PAYLOAD RAJAONGKIR', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'couriers' => $couriers,
                'price' => 'lowest',
            ]);

            $rajaOngkir = new RajaOngkirService();

            foreach ($couriers as $courier) {

                \Log::info('REQUEST RAJAONGKIR', [
                    'courier' => $courier,
                ]);

                $response = $rajaOngkir->calculateCost(
                    $origin,
                    $destination,
                    $weight,
                    $courier,
                    'lowest'
                );

                \Log::info('ONGKIR RESPONSE PARSED', [
                    'courier' => $courier,
                    'response' => $response
                ]);

                if (empty($response['data'])) {
                    continue;
                }

                foreach ($response['data'] as $service) {
                    // Gunakan kode kurir dari loop utama
                    $courierCode = $courier;
                
                    $shipper = Shipper::where('code', $courierCode)->first();
                    if (!$shipper) continue;

                    $result[] = [
                        'shipper_id' => $shipper->id,        // integer ID dari DB
                        'name'       => $shipper->name,      // nama kurir
                        'code'       => $courierCode,    // kode kurir
                        'service'    => $service['service'], // nama service
                        'description'=> $service['description'] ?? '',
                        'cost'       => (int)$service['cost'],
                        'etd'        => $service['etd'] ?? '-',
                    ];
                }
            }

            // URUTKAN DARI TERMURAH
            usort($result, fn ($a, $b) => $a['cost'] <=> $b['cost']);

            return response()->json($result);
        } catch (\Throwable $e) {

            \Log::error('SHIPPING COST ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

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

    public function preview($invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Decode JSON items & shipping
        $items = json_decode($order->items, true);
        $shipping = json_decode($order->shipping, true);
        $giftWrap = json_decode($order->gift_wrap, true);
        $giftWrapCost = $order->gift_wrap ? 10000 : 0;
        $isPickup = false;
        $subtotal = collect($items)->sum(function($item) {
            return $item['price'] * $item['qty'];
        });
        // Total ongkir
        $shippingCost = $order->total_ongkir ?? 0;

        return view('web.checkout.preview', compact('order', 'shippingCost', 'items', 'shipping', 'giftWrap', 'giftWrapCost', 'isPickup', 'subtotal'));
    }

}
