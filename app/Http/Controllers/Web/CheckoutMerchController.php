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
        // 1. Query Item dari Database (Bukan Session)
        $query = CartItem::with([
                    'merchProduct', 
                    'merchVariant.images', 
                    'merchSize'
                ])
                ->where('user_id', auth()->id());

        // 2. Filter: Hanya ambil item yang dicentang di halaman Cart
        if ($request->has('cart_item_ids')) {
            $query->whereIn('id', $request->cart_item_ids);
        }

        $cartItems = $query->get();

        // Validasi jika kosong
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Belum ada item yang dipilih untuk checkout!');
        }

        // 3. Logika Bungkus Kado
        $isGiftWrap = $request->has('wrap_product'); 
        $giftWrapPrice = $isGiftWrap ? 10000 : 0; 

        // 4. Mapping Data (Agar formatnya sama seperti struktur array teman Anda)
        $cart = $cartItems->map(function ($item) {
            // Logika Gambar
            $imagePath = '/img/default.png';
            if ($item->merchVariant && $item->merchVariant->images->isNotEmpty()) {
                $imagePath = $item->merchVariant->images->first()->image_path; 
            } elseif ($item->merchProduct && $item->merchProduct->images->isNotEmpty()) {
                $imagePath = $item->merchProduct->images->first()->image_path;
            }

            // Logika Nama
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
                // Data ID Asli untuk proses di backend
                'product_id' => $item->merch_product_id,
                'variant_id' => $item->merch_product_variant_id,
                'size_id'    => $item->merch_product_variant_size_id,
            ];
        });

        // 5. Hitung Total
        $subtotalBarang = $cart->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $totalQty = $cart->sum('quantity');
        $subtotal = $subtotalBarang + $giftWrapPrice;

        // Simpan ID item yang terpilih untuk dikirim ke view (Input Hidden)
        $selectedItemIds = $cartItems->pluck('id')->toArray();

        // 6. Data Pendukung (Sama seperti code Teman Anda)
        $addresses = UserAddress::where('user_id', auth()->id())->get();
        $shippers  = Shipper::all();
        $provinsi  = Provinsi::all();

        // Ubah collection jadi array
        $cart = $cart->toArray();

        return view('web.checkout.index', compact(
            'cart',
            'subtotal',
            'subtotalBarang',
            'giftWrapPrice',
            'totalQty',     
            'addresses',
            'shippers',
            'provinsi',
            // Kirim data ini untuk Input Hidden di view
            'selectedItemIds', 
            'isGiftWrap'
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
        $address = UserAddress::with(['provinsi','kabupaten'])
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
                'name'      => $address->name,
                'phone'     => $address->phone,
                'address'   => $address->address,
                'kecamatan' => $address->kecamatan->nama_kecamatan ?? '',
                'kabupaten' => $address->kabupaten->nama_kabupaten ?? '',
                'provinsi'  => $address->provinsi->nama_provinsi ?? '',
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
                'price' => 15000, // Bisa diganti logika API RajaOngkir
                'eta'   => '2-3 Hari', 
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