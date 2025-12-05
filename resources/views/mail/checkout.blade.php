<p>Hai {{ $bid->user->name ?? $bid->user->email }},</p>

<p>Selamat — Anda telah <strong>memenangkan</strong> lelang untuk:</p>
<ul>
    <li><strong>Produk:</strong> {{ $bid->product->title }}</li>
    <li><strong>Penawaran Anda:</strong> Rp {{ number_format($bid->price,0,',','.') }}</li>
    <li><strong>ID Produk:</strong> {{ $bid->product->id }}</li>
    <li><strong>Auction berakhir pada:</strong> {{ $bid->product->end_date ?? '—' }}</li>
</ul>

<p>Silakan selesaikan pembayaran agar proses dapat dilanjutkan. Jika ada instruksi pembayaran khusus, cek halaman checkout atau dashboard Anda.</p>

<center>
<a href="{{ route('checkout.cart', \Crypt::encrypt($bid->product->slug)) }}" style="background-color:#5b9aff;border-radius:4px;color:#ffffff;padding:0 24px;margin-bottom:20px;font-family:'Lato';font-size:14px;font-weight:bold;line-height:50px;display:inline-block;text-decoration:none" target="_blank">Selesaikan Pembayaran</a>
</center>

<p><strong>Perhatian:</strong> Harap selesaikan pembayaran dalam 48 jam setelah menerima email ini. Jika pembayaran tidak diterima dalam jangka waktu yang ditentukan, kemenangan dapat dibatalkan.</p>

<p>Jika butuh bantuan, hubungi tim support melalui halaman kontak atau email admin.</p>

<br>
<p>Terima kasih,</p>
<p>Tim Lelang</p>