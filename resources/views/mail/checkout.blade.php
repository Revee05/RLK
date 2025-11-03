<p>Hi {{$bid->user->email}}</p>
<p>Selamat Anda telah memenangkan lelang {{$bid->product->title}} - seharga Rp {{number_format($bid->price)}}.</p>
<p>Mohon segera menyelesaikan pembayaran.</p>
<center>
<a href="{{route('checkout.cart',\Crypt::encrypt($bid->product->slug))}}" style="background-color:#5b9aff;border-radius:4px;color:#ffffff;padding:0 24px;margin-bottom:20px;font-family:'Lato';font-size:14px;font-weight:bold;line-height:50px;display:inline-block;text-decoration:none" target="_blank">Checkout</a>
</center>
<br>
<p>Terima Kasih</p>
<p>Lelang</p>