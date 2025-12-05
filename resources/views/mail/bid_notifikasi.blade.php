<p>Halo {{ $bid->user->name ?? $bid->user->email }},</p>

<p>Terima kasih telah berpartisipasi dalam lelang untuk <strong>{{ $bid->product->title }}</strong>.</p>

<ul>
    <li><strong>Penawaran tertinggi Anda pada produk ini:</strong> Rp {{ number_format($bid->price,0,',','.') }}</li>
    <li><strong>ID Produk:</strong> {{ $bid->product->id }}</li>
    <li><strong>Status lelang:</strong> Telah berakhir</li>
</ul>

<p>Sayangnya Anda <strong>belum memenangkan</strong> lelang ini. Harga akhir pemenang:</p>
<p>
@if(!empty($winnerPrice))
    <strong>Rp {{ number_format($winnerPrice,0,',','.') }}</strong>
@elseif(!empty($winnerBid) && !empty($winnerBid->price))
    <strong>Rp {{ number_format($winnerBid->price,0,',','.') }}</strong>
@else
    <em>(tidak tersedia)</em>
@endif
</p>

@if(!empty($winnerBid) && !empty($winnerBid->user))
<p>Pemenang: <strong>{{ $winnerBid->user->name ?? $winnerBid->user->email }}</strong></p>
@endif

<p>Anda dapat:</p>
<ul>
    <li>Melihat detail lelang di dashboard atau halaman produk.</li>
    <li>Mencari lelang lain yang menarik di situs kami.</li>
</ul>

<p>Terima kasih telah mengikuti lelang. Semoga beruntung di kesempatan berikutnya.</p>

<br>
<p>Salam,</p>
<p>Tim Lelang</p>