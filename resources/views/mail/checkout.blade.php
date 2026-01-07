<p>Hai {{ $bid->user->name ?? $bid->user->email }},</p>

<p>Selamat — Anda telah <strong>memenangkan</strong> lelang untuk:</p>
<ul>
    <li><strong>Produk:</strong> {{ $bid->product->title }}</li>
    <li><strong>Penawaran Anda:</strong> Rp {{ number_format($bid->price,0,',','.') }}</li>
    <li><strong>ID Produk:</strong> {{ $bid->product->id }}</li>
    <li><strong>Auction berakhir pada:</strong> {{ $bid->product->end_date ?? '—' }}</li>
</ul>

<p>Produk lelang telah kami masukkan ke <strong>keranjang Anda</strong>. Silakan selesaikan checkout dalam waktu <strong>7 hari</strong> sebelum masa berlaku berakhir.</p>

<center>
<a href="{{ route('cart.index') }}" style="background-color:#5b9aff;border-radius:4px;color:#ffffff;padding:0 24px;margin-bottom:20px;font-family:'Lato';font-size:14px;font-weight:bold;line-height:50px;display:inline-block;text-decoration:none" target="_blank">Lihat Keranjang & Checkout</a>
</center>

<p><strong>Perhatian:</strong> Harap selesaikan pembayaran dalam  7 x 24 jam setelah menerima email ini. Jika pembayaran tidak diterima dalam jangka waktu yang ditentukan, kemenangan dapat dibatalkan.</p>

<p>Jika butuh bantuan, hubungi tim support melalui halaman kontak atau email admin.</p>

<br>
<p>Terima kasih,</p>
<p>Tim Rasa Lelang Karya</p>