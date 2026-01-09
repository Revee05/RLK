@component('mail::message')
# ✅ Pembayaran Berhasil

Halo {{ $order->user->name ?? 'Pelanggan' }},

Terima kasih telah berbelanja di **{{ config('app.name') }}**.  
Pembayaran untuk pesanan Anda telah kami terima dengan detail sebagai berikut:

---

## 🧾 Informasi Pesanan
- **Nomor Invoice:** {{ $order->invoice }}
- **Tanggal Pembayaran:** {{ optional($order->paid_at ?? $order->updated_at)->format('d M Y, H:i') }}
- **Metode Pembayaran:** {{ strtoupper($order->payment_channel ?? '-') }}

---

## 📦 Rincian Produk
@php
    $items = is_array($items ?? null) ? $items : [];
    $subtotalProduk = 0;
    $orderType = $orderType ?? 'merch'; // Default to merch for backward compatibility
@endphp

@foreach($items as $item)
@php
    $qty   = $item['qty'] ?? $item['quantity'] ?? 1;
    $price = (int) ($item['price'] ?? 0);
    $lineTotal = $price * $qty;
    $subtotalProduk += $lineTotal;
    
    // Get product name based on order type
    if ($orderType === 'lelang') {
        $productName = $item['name'] ?? $item['title'] ?? 'Produk Lelang';
        $variantInfo = null;
        $sizeInfo = null;
    } else {
        $productName = $item['name'] ?? $item['merch_product']['name'] ?? 'Produk';
        $variantInfo = $item['variant_name'] ?? $item['merch_variant']['name'] ?? null;
        $sizeInfo = $item['size_name'] ?? $item['merch_size']['name'] ?? null;
    }
@endphp

- **{{ $productName }}**
  @if($variantInfo)
    <br>Varian: {{ $variantInfo }}
  @endif
  @if($sizeInfo)
    <br>Ukuran: {{ $sizeInfo }}
  @endif
  <br>Jumlah: {{ $qty }}
  <br>Harga Satuan: Rp {{ number_format($price,0,',','.') }}
  <br>Subtotal Item: Rp {{ number_format($lineTotal,0,',','.') }}
@endforeach

---

@php
    // Use passed shipping data if available, otherwise parse from order
    $shippingData = $shipping ?? (json_decode($order->shipping ?? '{}', true) ?? []);
    $shippingType = $shippingData['type'] ?? 'delivery';
    $shippingName = $shippingData['name'] ?? '-';
    $shippingService = $shippingData['service'] ?? '-';
    $shippingCost = $shippingData['cost'] ?? 0;
    $pickupAddress = "Griya Jl. Sekargading blok C 19, RT.04/RW.03, Kel. Kalisegoro, Gunung Pati, Kota Semarang, Jawa Tengah 50228";
@endphp

## 🚚 Informasi Pengiriman
@if($shippingType === 'pickup')
- **Tipe Pengiriman:** {{ $shippingName }}
- **Alamat Pengambilan:** {{ $pickupAddress }}
@else
- **Tipe Pengiriman:** Delivery
- **Alamat Pengiriman:**  
  {{ $order->address->name ?? '-' }} - {{ $order->address->phone ?? '-' }}<br>
  {{ $order->address->address ?? '-' }},  
  {{ $order->address->district->name ?? '-' }}<br>
  {{ $order->address->city->name ?? '-' }},  
  {{ $order->address->province->name ?? '-' }}
- **Jasa Pengiriman:** {{ $shippingName }} - {{ $shippingService }}
- **Biaya Pengiriman:** Rp {{ number_format($shippingCost,0,',','.') }}
@endif

---

## 💰 Ringkasan Pembayaran
@php
    $biayaPackaging = !empty($order->gift_wrap) ? 10000 : 0;
@endphp

- **Subtotal Produk:** Rp {{ number_format($subtotalProduk,0,',','.') }}

@if($biayaPackaging > 0)
- **Biaya Pengemasan:** Rp {{ number_format($biayaPackaging,0,',','.') }}
@endif

@if(!empty($order->total_ongkir))
- **Biaya Pengiriman:** Rp {{ number_format($order->total_ongkir,0,',','.') }}
@endif

---

- **Total Dibayar:**  
  **Rp {{ number_format($order->total_tagihan,0,',','.') }}**

---

Pesanan Anda akan segera kami proses.  
Jika Anda memiliki pertanyaan, silakan hubungi tim kami kapan saja.

Terima kasih atas kepercayaan Anda berbelanja di **{{ config('app.name') }}** 🙏

Salam hangat,  
**{{ config('app.name') }}**
@endcomponent
