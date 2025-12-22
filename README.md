
status product:
- 1 => publish
- 2 => sudah ada pemenang, tunggu untuk checkout
- 3 => Expired karena tidak diCO pemenang


Cara Testing Manual (Sebelum Tunggu Otomatis)

Test Masuk Keranjang:
- Set satu produk lelang di database: end_date buat jadi 1 menit yang lalu.
- Pastikan ada bids untuk produk itu.
- Jalankan: php artisan lelang:close-expired

Cek: Lihat tabel cart_items, harusnya item masuk dengan expires_at 7 hari ke depan.

Test Hapus Otomatis (Hangus):
- Edit manual salah satu data di tabel cart_items (yang tipe lelang).
- Ubah kolom expires_at jadi kemarin (waktu lampau).
- Jalankan: php artisan cart:cleanup-auction

Cek: Item harusnya hilang dari tabel cart_items, dan di tabel products statusnya jadi 3 (Hangus).
