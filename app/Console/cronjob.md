---
Dokumentasi Teknis: Sistem Otomatisasi & Penentuan Pemenang Lelang

Dokumen ini menjelaskan alur backend untuk menentukan pemenang lelang secara otomatis saat waktu lelang berakhir, serta bagaimana frontend menampilkan status "MENANG" atau "KALAH".

1. Ringkasan Alur Kerja
- User melakukan penawaran (bid) pada sebuah produk.
- Cron job dijalankan secara berkala (mis. setiap menit) untuk mengecek produk dengan `end_date` yang telah lewat.
- Backend memilih penawar tertinggi untuk tiap produk yang berakhir.
- Database diperbarui: status produk dan `winner_id` diisi sesuai hasil.
- Frontend menampilkan status hasil lelang untuk user yang login.

2. Struktur Database

A. Tabel `products`
- `id` (PK)
- `status` (INT): 1 = Live, 2 = Sold, 3 = Expired/Hangus
- `end_date` (DATETIME)
- `winner_id` (INT, nullable)

B. Tabel `bids`
- `product_id` (FK ke `products`)
- `user_id` (penawar)
- `price` (nominal tawaran)

3. Backend (Laravel)

A. Model
- `App\\Product`
- `App\\Bid`

B. Console Command (Logika Penutupan Lelang)
File: `app/Console/Commands/CloseExpiredAuctions.php`

Langkah utama:
1. Ambil semua produk dengan `status = 1` (Live) dan `end_date` < now().
2. Untuk setiap produk:
   - Cari bid tertinggi: `->orderBy('price', 'desc')->first()`.
   - Jika ada bid: set `status = 2` (Sold) dan `winner_id` = ID penawar tertinggi.
   - Jika tidak ada bid: set `status = 3` (Expired/Hangus).
3. Simpan perubahan ke database.

C. Scheduler (Cron Job)
File: `app/Console/Kernel.php`

Contoh penjadwalan:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('lelang:close-expired')->everyMinute();
}
```

4. Frontend (Blade / JavaScript)

A. Response JSON yang Disarankan
Pastikan controller yang mengembalikan data produk menyertakan minimal field berikut:
- `status`
- `end_date_iso` (ISO 8601, untuk perhitungan waktu di frontend)
- `winner_id`
- `is_winner` (opsional, boolean)

B. Contoh Logika Tampilan (JavaScript)
```js
if (product.status === 2 || isExpired) {
  if (currentUserId && product.winner_id == currentUserId) {
    // tampilkan badge "MENANG" (warna hijau)
  } else {
    // tampilkan badge "KALAH" (warna abu-abu)
  }
}
```

5. Menjalankan Secara Manual (Testing)

Untuk menguji command tanpa menunggu cron job:
```
php artisan lelang:close-expired
```
Setelah dijalankan, periksa tabel `products` — kolom `winner_id` harus terisi untuk produk yang terjual.

Catatan: jangan lupa jalankan `php artisan migrate` jika ada migrasi yang belum diterapkan.

6. Troubleshooting

Jika badge "MENANG" tidak muncul padahal user adalah pemenang, periksa:
- Apakah `winner_id` pada tabel `products` sudah terisi?
- Apakah controller mengirim `winner_id` dalam respons JSON?
- Apakah variabel `currentUserId` di frontend berisi ID user yang login?
- Apakah tipe data cocok (String vs Integer)? Gunakan pembandingan longgar `==` atau lakukan konversi tipe jika perlu.

7. Test Hapus Otomatis (Hangus) — `cart_items`

Langkah untuk menguji pembersihan item cart jenis lelang:
1. Edit salah satu baris pada tabel `cart_items` (tipe lelang).
2. Ubah kolom `expires_at` menjadi waktu lampau (mis. kemarin).
3. Jalankan command pembersihan:
```
php artisan cart:cleanup-auction
```
4. Verifikasi hasil:
- Item harus terhapus dari tabel `cart_items`.
- Di tabel `products`, status terkait harus menjadi `3` (Hangus/Expired).

---
Pembaruan ini merapikan struktur dokumen dan menambahkan contoh kode singkat untuk referensi implementasi.
________________________________________
Dokumentasi Teknis: Sistem Otomatisasi & Penentuan Pemenang Lelang
Dokumen ini menjelaskan cara kerja sistem dalam menentukan pemenang lelang secara otomatis saat waktu lelang berakhir, termasuk alur backend dan cara frontend menampilkan status “MENANG” atau “KALAH”.
________________________________________
1. Alur Kerja Sistem (Workflow)
1.	User melakukan bid pada sebuah produk.
2.	Cron Job berjalan otomatis (misalnya setiap menit) untuk mengecek produk yang sudah melewati waktu end_date.
3.	Backend menentukan penawar tertinggi.
4.	Database diperbarui:
o	Status produk berubah menjadi Sold.
o	Kolom winner_id diisi dengan ID pemenang.
5.	Frontend menampilkan status berdasarkan apakah user yang login adalah pemenang.
________________________________________
2. Struktur Database
A. Tabel: products
Kolom	Tipe	Keterangan
id	PK	ID Produk
status	INT	1 = Live, 2 = Sold, 3 = Expired
end_date	DATETIME	Waktu akhir lelang
winner_id	INT (nullable)	ID pemenang lelang
B. Tabel: bids
Kolom	Keterangan
product_id	ID Produk
user_id	ID User
price	Nominal tawaran
________________________________________
3. Backend (Laravel)
A. Model
•	App\Product
•	App\Bid
Digunakan untuk berinteraksi dengan database.
________________________________________
B. Console Command – Otak Sistem
File: app/Console/Commands/CloseExpiredAuctions.php
Logika Utama:
1.	Ambil semua produk status = 1 dan end_date < now().
2.	Untuk setiap produk:
o	Cari bid tertinggi dengan orderBy('price','desc').
3.	Jika ada bid:
o	Ubah status → 2 (Sold).
o	Isi winner_id → ID user penawar tertinggi.
4.	Jika tidak ada bid:
o	Ubah status → 3 (Expired).
5.	Simpan perubahan.
________________________________________
C. Scheduler (Cron Job)
File: app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('lelang:close-expired')->everyMinute();
}
________________________________________
4. Frontend (Blade / JavaScript)
A. Response JSON yang Harus Disertakan
Pastikan controller mengirim field berikut:
•	status
•	end_date_iso
•	winner_id
•	is_winner (opsional, boolean)
________________________________________
B. Logika Tampilan (JavaScript)
if (product.status === 2 || isExpired) {

    if (currentUserId && product.winner_id == currentUserId) {
        return "MENANG"; // badge hijau
    } else {
        return "KALAH"; // badge abu-abu
    }

}
________________________________________
5. Cara Menjalankan Manual (Testing)
Untuk mengecek script tanpa menunggu cron job:
php artisan lelang:close-expired
Setelah itu, cek tabel products → kolom winner_id harus terisi.
________________________________________
6. Troubleshooting Checklist
Jika badge MENANG tidak tampil meski user adalah pemenang:
•	Apakah winner_id pada tabel products sudah berisi?
•	Apakah controller mengirim winner_id dalam JSON?
•	Apakah currentUserId di frontend berisi ID user yang login?
•	Apakah tipe data cocok? (String vs Integer — gunakan == atau lakukan konversi)
________________________________________

jangan lupa jalan kan php artisan migrate


Test Hapus Otomatis (Hangus):

Edit manual salah satu data di tabel cart_items (yang tipe lelang).

Ubah kolom expires_at jadi kemarin (waktu lampau).
```
php artisan cart:cleanup-auction
```

Cek: Item harusnya hilang dari tabel cart_items, dan di tabel products statusnya jadi 3 (Hangus).
