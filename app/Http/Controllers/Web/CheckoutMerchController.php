<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserAddress;
use App\Shipper;
use App\OrderMerch;
use App\Order;
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
    /*
    |--------------------------------------------------------------------------
    | MAIN CHECKOUT METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Display checkout page with selected cart items
     * Supports both merchandise and auction products
     */
    public function index(Request $request)
    {
        \Log::info('=== CHECKOUT INDEX START ===', [
            'request_all' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Validate cart item selection
        if (!$request->has('cart_item_ids') || empty($request->input('cart_item_ids'))) {
            return redirect()->route('cart.index')
                ->with('error', 'Silakan pilih minimal satu produk untuk checkout!');
        }

        // Fetch selected cart items with relations
        $cartItems = CartItem::with(['merchProduct', 'merchVariant.images', 'merchSize'])
            ->where('user_id', auth()->id())
            ->whereIn('id', $request->input('cart_item_ids'))
            ->get();

        \Log::info('CHECKOUT INDEX: Cart items fetched', [
            'count' => $cartItems->count(),
            'items' => $cartItems->map(fn($item) => [
                'id' => $item->id,
                'type' => $item->type,
                'product_id' => $item->product_id,
                'merch_product_id' => $item->merch_product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ])->toArray(),
        ]);

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

        // Gift wrap option
        $isGiftWrap = $request->has('wrap_product');
        $giftWrapPrice = $isGiftWrap ? 10000 : 0;

        // Map cart items for view display
        $cart = $cartItems->map(function ($item) {
            // Determine product image (variant first, then product, then default)
            $imagePath = '/img/default.png';
            if ($item->merchVariant && $item->merchVariant->images->isNotEmpty()) {
                $imagePath = $item->merchVariant->images->first()->image_path;
            } elseif ($item->merchProduct && $item->merchProduct->images->isNotEmpty()) {
                $imagePath = $item->merchProduct->images->first()->image_path;
            }

            $productName = $item->merchProduct->name ?? 'Unknown Product';

            return [
                'id'       => $item->id,
                'name'     => $productName,
                'price'    => $item->price,
                'quantity' => $item->quantity,
                'image'    => $imagePath,

                // Variant information
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

        // Calculate totals
        $subtotalBarang = $cart->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $totalQty = $cart->sum('quantity');
        $subtotal = $subtotalBarang;
        $selectedItemIds = $cartItems->pluck('id')->toArray();

        // Fetch supporting data (addresses, shippers, provinces)
        $addresses = UserAddress::where('user_id', auth()->id())
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();
        
        $shippers = Shipper::all();
        $province = Province::all();

        $selectedAddressId = session('checkout_address_id');

        $selectedAddress = $selectedAddressId
            ? UserAddress::with(['province', 'city', 'district'])->find($selectedAddressId)
            : auth()->user()->userAddress()->with(['province', 'city', 'district'])->first();

        \Log::info('CHECKOUT INDEX: View data prepared', [
            'cart_count' => $cart->count(),
            'subtotal' => $subtotal,
            'gift_wrap' => $giftWrapPrice,
            'total_qty' => $totalQty,
            'addresses_count' => $addresses->count(),
            'selected_address_id' => $selectedAddress->id ?? null,
            'selected_item_ids' => $selectedItemIds,
        ]);

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
     * Process checkout and route to appropriate handler
     * (Merchandise or Auction)
     */
    public function process(Request $request)
    {
        \Log::info('=== CHECKOUT PROCESS START ===', $request->all());

        // Validate request
        $request->validate([
            'address_id'        => 'required',
            'shipping_method'   => 'required',
            'selected_item_ids' => 'required',
        ]);

        $shippingMethod = $request->shipping_method;
        $selectedIds = session('checkout_selected_item_ids', []);

        if (empty($selectedIds)) {
            \Log::warning('Checkout PROCESS: selected_item_ids kosong');
            return redirect()->route('cart.index')->with('error', 'Data item tidak valid.');
        }

        // Ambil Item dari Database (HANYA YANG DIPILIH)
        $cartItems = CartItem::with(['merchProduct', 'merchVariant.images', 'merchSize', 'auctionProduct'])
                    ->where('user_id', auth()->id())
                    ->whereIn('id', $selectedIds) // <--- Ini perbaikan kuncinya
                    ->get();

        \Log::info('Checkout PROCESS: Cart items loaded', [
            'count' => $cartItems->count(),
            'items' => $cartItems->map(fn($item) => [
                'id' => $item->id,
                'type' => $item->type,
                'product_id' => $item->product_id,
                'merch_product_id' => $item->merch_product_id,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ])->toArray(),
        ]);

        if ($cartItems->isEmpty()) {
            \Log::warning('Checkout PROCESS: item cart tidak ditemukan di DB');
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong atau item tidak ditemukan.');
        }

        // Detect cart type: lelang vs merchandise
        $hasLelang = $cartItems->contains(fn($item) => $item->type === 'lelang');
        $hasMerch = $cartItems->contains(fn($item) => $item->type !== 'lelang');

        \Log::info('Checkout PROCESS: Type detection', [
            'has_lelang' => $hasLelang,
            'has_merch' => $hasMerch,
            'types_breakdown' => $cartItems->groupBy('type')->map->count()->toArray(),
        ]);

        // Validate: cannot mix lelang and merchandise in one checkout
        if ($hasLelang && $hasMerch) {
            \Log::warning('Checkout PROCESS: Mixed cart types not allowed');
            return redirect()->route('cart.index')
                ->with('error', 'Tidak dapat checkout produk lelang dan merchandise secara bersamaan.');
        }

        // Route to appropriate processor
        return $hasLelang 
            ? $this->processLelangCheckout($request, $cartItems, $selectedIds)
            : $this->processMerchCheckout($request, $cartItems, $selectedIds);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT PROCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Process merchandise checkout
     * Creates OrderMerch record
     */
    protected function processMerchCheckout(Request $request, $cartItems, $selectedIds)
    {
        \Log::info('=== PROCESS MERCH CHECKOUT START ===', [
            'items_count' => $cartItems->count(),
            'address_id' => $request->address_id,
            'shipping_method' => $request->shipping_method,
        ]);

        $shippingMethod = $request->shipping_method;

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

        \Log::info('Checkout MERCH: Ready to create order', [
            'items_count' => count($orderItems),
            'items' => $orderItems,
            'shipping' => $shippingData,
            'subtotal' => $totalBarang,
            'shipping_cost' => $totalOngkir,
            'total' => $totalTagihan,
            'address_id' => $request->address_id,
            'shipper_id' => $shipperId,
        ]);

        try {
            DB::beginTransaction();

            // Create OrderMerch record
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

            // Delete processed items from cart
            CartItem::whereIn('id', $selectedIds)->delete();

            DB::commit();

            session()->forget(['checkout_selected_item_ids', 'checkout_gift_wrap']);
           
            \Log::info('Checkout MERCH: Order created successfully', [
                'order_id' => $order->id,
                'invoice'  => $order->invoice,
                'user_id'  => auth()->id(),
            ]);

            return redirect()->route('checkout.preview', ['invoice' => $order->invoice]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Checkout MERCH: Failed to create order', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'stack' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Process auction (lelang) checkout
     * Creates Order record with unified fields
     */
    protected function processLelangCheckout(Request $request, $cartItems, $selectedIds)
    {
        \Log::info('=== PROCESS LELANG CHECKOUT START ===', [
            'items_count' => $cartItems->count(),
            'address_id' => $request->address_id,
            'shipping_method' => $request->shipping_method,
        ]);

        $shippingMethod = $request->shipping_method;

        // Map auction items for order creation
        $orderItems = $cartItems->map(function ($item) {
            $product = $item->auctionProduct;
            $imagePath = '/img/default.png';
            
            if ($product) {
                if ($product->imageUtama && $product->imageUtama->path) {
                    $imagePath = $product->imageUtama->path;
                } elseif ($product->images && $product->images->count() > 0) {
                    $imagePath = $product->images->first()->path ?? $imagePath;
                }
            }

            return [
                'id'           => $item->id,
                'name'         => $product->title ?? 'Produk Lelang',
                'price'        => $item->price, // Harga bid pemenang
                'qty'          => 1, // Lelang selalu qty = 1
                'image'        => $imagePath,
                'product_id'   => $item->product_id,
                'type'         => 'lelang',
            ];
        });

        $selectedIds = $cartItems->pluck('id')->toArray();
        
        // Hitung Total
        $totalBarang = $cartItems->sum('price'); // Quantity lelang = 1

        $shipperId = null;
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
            $shipperCode = $request->shipping_code;
            $shipper = Shipper::where('code', $shipperCode)->first();
            $shipperId = $shipper ? $shipper->id : null;
    
            $shippingData = [
                'type'        => 'delivery',
                'name'        => $request->shipping_name,
                'code'        => $request->shipping_code,
                'service'     => $request->shipping_service,
                'description' => $request->shipping_description ?? null,
                'cost'        => $totalOngkir,
                'etd'         => $request->shipping_etd,
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

        $totalTagihan = $totalBarang + $totalOngkir;

        \Log::info('Checkout LELANG: Ready to create order', [
            'items_count' => count($orderItems),
            'items' => $orderItems,
            'shipping' => $shippingData,
            'subtotal' => $totalBarang,
            'shipping_cost' => $totalOngkir,
            'total' => $totalTagihan,
            'address_id' => $request->address_id,
            'shipper_id' => $shipperId,
        ]);

        try {
            DB::beginTransaction();

            $invoice = 'INV-' . strtoupper(Str::random(10));

            // Fetch address for required legacy fields
            $address = UserAddress::find($request->address_id);
            if (!$address) {
                throw new \Exception('Alamat tidak ditemukan');
            }

            // Create Order with unified + legacy fields
            $order = Order::create([
                'user_id'       => auth()->id(),
                'address_id'    => $request->address_id,
                
                // Field baru (unified)
                'items'         => json_encode($orderItems),
                'shipping'      => json_encode($shippingData),
                'shipper_id'    => $shipperId,
                'total_ongkir'  => $totalOngkir,
                'total_tagihan' => $totalTagihan,
                'invoice'       => $invoice,
                'status'        => 'pending',
                'note'          => $request->note ?? '',
                'payment_method' => 'xendit',
                
                // Field lama REQUIRED (untuk backward compatibility)
                'name'          => $address->name,
                'phone'         => $address->phone,
                'label_address' => $address->label_address ?? 'Default',
                'address'       => $address->address,
                'provinsi_id'   => $address->province_id,
                'kabupaten_id'  => $address->city_id,
                'kecamatan_id'  => $address->district_id,
                'product_id'    => $cartItems->first()->product_id, // Untuk relasi lama
                'bid_terakhir'  => $cartItems->first()->price,
                'jenis_ongkir'  => $shippingData['name'] ?? 'Regular',
                'pengirim'      => $shippingData['name'] ?? 'pickup',
                
                // Generate UUID untuk backward compat
                'orderid_uuid'  => Str::uuid(),
                'order_invoice' => $invoice,
                'payment_status' => 1, // pending
            ]);

            // Hapus dari cart
            CartItem::whereIn('id', $selectedIds)->delete();

            DB::commit();

            session()->forget(['checkout_selected_item_ids', 'checkout_gift_wrap']);
           
            \Log::info('Checkout LELANG: Order created successfully', [
                'order_id' => $order->id,
                'invoice'  => $order->invoice,
                'user_id'  => auth()->id(),
            ]);

            return redirect()->route('checkout.preview', ['invoice' => $order->invoice]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Checkout LELANG: Failed to create order', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'stack' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()->with('error', 'Gagal memproses pesanan lelang: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Set selected address for checkout
     */
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

    /**
     * Calculate shipping (flat rate from shippers table)
     */
    public function calculateShipping(Request $request)
    {
        // Get all shippers from database
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

    /**
     * Get shipping cost from RajaOngkir API
     */
    public function getShippingCost(Request $request)
    {
        \Log::info('=== SHIPPING COST REQUEST ===', [
            'request_all' => $request->all(),
        ]);

        // Validate origin and destination
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

                \Log::info('RajaOngkir: Response parsed', [
                    'courier' => $courier,
                    'response' => $response
                ]);

                if (empty($response['data'])) {
                    continue;
                }

                foreach ($response['data'] as $service) {
                    $courierCode = $courier;
                    $shipper = Shipper::where('code', $courierCode)->first();
                    if (!$shipper) continue;

                    $result[] = [
                        'shipper_id'  => $shipper->id,
                        'name'        => $shipper->name,
                        'code'        => $courierCode,
                        'service'     => $service['service'],
                        'description' => $service['description'] ?? '',
                        'cost'        => (int)$service['cost'],
                        'etd'         => $service['etd'] ?? '-',
                    ];
                }
            }

            // Sort by cost (cheapest first)
            usort($result, fn ($a, $b) => $a['cost'] <=> $b['cost']);

            return response()->json($result);
            
        } catch (\Throwable $e) {
            \Log::error('RajaOngkir: Failed to get shipping cost', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Gagal mengambil data kurir.'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Display order success page
     */
    public function success($invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();
        return view('web.checkout.success', compact('order'));
    }

    /**
     * Display order preview before payment
     * Supports both OrderMerch and Order (lelang)
     */
    public function preview($invoice)
    {
        // Try OrderMerch first (merchandise)
        $order = OrderMerch::where('invoice', $invoice)
            ->where('user_id', auth()->id())
            ->first();

        // If not found, try Order (lelang)
        if (!$order) {
            $order = Order::where('invoice', $invoice)
                ->where('user_id', auth()->id())
                ->first();
            
            if (!$order) {
                abort(404, 'Order tidak ditemukan');
            }
            
            // Mark as lelang order for view
            $order->order_type = 'lelang';
        } else {
            $order->order_type = 'merch';
        }

        // Decode JSON items & shipping
        $items = json_decode($order->items, true);
        $shipping = json_decode($order->shipping, true);
        $giftWrap = $order->gift_wrap ?? false;
        $giftWrapCost = $giftWrap ? 10000 : 0;
        $isPickup = ($shipping['type'] ?? '') === 'pickup';
        $subtotal = collect($items)->sum(function($item) {
            return $item['price'] * ($item['qty'] ?? 1);
        });
        // Total ongkir
        $shippingCost = $order->total_ongkir ?? 0;

        return view('web.checkout.preview', compact('order', 'shippingCost', 'items', 'shipping', 'giftWrap', 'giftWrapCost', 'isPickup', 'subtotal'));
    }

}
