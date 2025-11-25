<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserAddress;
use App\Shipper;
use App\OrderMerch;
use App\CartItem; // Model Cart Database (Punya Anda)
use Illuminate\Support\Str;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use Illuminate\Support\Facades\DB;


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
            if($item->merchSize) {
                $productName .= ' (' . $item->merchSize->size . ')';
            }

            return [
                'id'       => $item->id,
                'name'     => $productName,
                'price'    => $item->price,
                'quantity' => $item->quantity,
                'image'    => $imagePath,
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
        $subtotal = $subtotalBarang + $giftWrapPrice;

        // Simpan ID item yang terpilih untuk dikirim ke view (Input Hidden)
        $selectedItemIds = $cartItems->pluck('id')->toArray();

        // ==================================================================
        // 5. DATA PENDUKUNG (Alamat, Kurir, Provinsi)
        // ==================================================================
        $addresses = UserAddress::where('user_id', auth()->id())->get();

        // Ambil shipper
        $shippers = Shipper::all();

        // Ambil semua provinsi
        $provinsi = Provinsi::all();

        return view('web.checkout.index', compact(
            'cart',
            'subtotal',
            'subtotalBarang',
            'giftWrapPrice',
            'totalQty',     
            'addresses',
            'shippers',
            'provinsi'
        ));
    }

    // ==================================================================
    // METHOD PROCESS: GABUNGAN FIX (Logic DB Anda + Struktur Teman)
    // ==================================================================
    public function process(Request $request)
    {
        $request->validate([
            'address_id'        => 'required',
            'shipping_method'   => 'required',
            'selected_item_ids' => 'required', // Wajib ada (dari input hidden)
        ]);

        // 1. Decode ID Item yang dipilih dari View
        $selectedIds = json_decode($request->selected_item_ids, true);

        if (empty($selectedIds)) {
            return redirect()->route('cart.index')->with('error', 'Data item tidak valid.');
        }

        // 2. Ambil Item dari Database (HANYA YANG DIPILIH)
        $cartItems = CartItem::where('user_id', auth()->id())
                    ->whereIn('id', $selectedIds) // <--- Ini perbaikan kuncinya
                    ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong atau item tidak ditemukan.');
        }

        // Hitung Total Barang
        $totalBarang = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        // 3. Handle Biaya-biaya
        $totalOngkir = $request->total_ongkir ?? 0;
        
        // Handle Bungkus Kado (Dari Input Hidden)
        $biayaLayanan = 0;
        if ($request->has('is_gift_wrap') && $request->is_gift_wrap == '1') {
            $biayaLayanan = 10000;
        }

        $shipperId = $request->shipping_method == 'pickup' ? null : $request->selected_shipper_id; 
        $totalTagihan = $totalBarang + $totalOngkir + $biayaLayanan;

        try {
            DB::beginTransaction();

            // 4. Buat Order (Struktur sama dengan punya Teman Anda)
            $order = OrderMerch::create([
                'user_id'       => auth()->id(),
                'address_id'    => $request->address_id,
                // Simpan snapshot item sebagai JSON
                'items'         => $cartItems->toJson(), 
                'shipper_id'    => $shipperId,
                'jenis_ongkir'  => $request->jenis_ongkir ?? 'Regular', 
                'total_ongkir'  => $totalOngkir,
                'total_tagihan' => $totalTagihan,
                'invoice'       => 'INV-' . strtoupper(Str::random(10)),
                'status'        => 'pending',
                // Tambahkan catatan jika ada bungkus kado
                'note'          => $request->note . ($biayaLayanan > 0 ? ' ( + Gift Wrap)' : ''),
            ]);

            // 5. Hapus HANYA Item yang diproses dari Database Cart
            CartItem::whereIn('id', $selectedIds)->delete();

            DB::commit();

            return redirect()->route('checkout.success', $order->invoice);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    // ==================================================================
    // METHOD DI BAWAH INI TETAP (Sesuai Code Teman Anda)
    // ==================================================================

    public function setAddress(Request $request)
    {
        $address = UserAddress::with(['province','city','district'])
                    ->find($request->address_id);

        if(!$address){
            return response()->json(['status'=>'error']);
        }

        // Simpan pilihan alamat ke session (agar persist saat refresh)
        session(['checkout_address' => $address]);

        return response()->json([
            'status' => 'success',
            'address' => [
                'label_address' => $address->label_address,
                'name' => $address->name,
                'phone' => $address->phone,
                'address' => $address->address,
                'kecamatan' => $address->kecamatan->nama_kecamatan ?? '',
                'kabupaten' => $address->kabupaten->nama_kabupaten ?? '',
                'provinsi' => $address->provinsi->nama_provinsi ?? '',
            ]
        ]);
    }

    public function calculateShipping(Request $request)
    {
        $shippers = Shipper::select('id', 'name')->get();
        $result = [];
        foreach ($shippers as $ship) {
            $result[] = [
                'id'    => $ship->id,
                'name'  => $ship->name,
                'price' => 0,       // flat 0
                'eta'   => '-',     // default
            ];
        }
        return response()->json($result);
    }


    public function success($invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();
        return view('web.checkout.success', compact('order'));
    }
}