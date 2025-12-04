# Testing Real-time Bidding

## 🔧 Langkah Perbaikan Terbaru

### Event Broadcasting Configuration
✅ Changed `ShouldBroadcast` → `ShouldBroadcastNow` (broadcast immediately, no queue)
✅ Added `broadcastWith()` method untuk explicit data control
✅ Added comprehensive logging di setiap event broadcast
✅ Fixed global function scope (`window.formatRp`, `window.updateNominalDropdown`)

## 🧪 Quick Test Pusher Connection

1. **Buka di browser:** `http://localhost/test-pusher.html`
2. **Cek status:** Harus muncul "✅ Connected to Pusher!"
3. **Jika error:** Cek Pusher credentials di `.env`

## Cara Test Fitur Real-time

### 1. Buka Browser Console (F12)
Saat membuka halaman detail produk, cek console browser. Anda harus melihat log:
```
[Echo] Mendengarkan channel product.{id}
[Pusher] Connected to WebSocket
Vue initialized. slug = {slug}
[Echo] ✓ Listener berhasil didaftarkan
```

### 2. Test dengan 2 Browser/Tab
1. Buka halaman detail produk yang sama di **2 browser berbeda** (atau 2 tab incognito)
2. Login sebagai **user berbeda** di masing-masing browser
3. Lakukan bid dari Browser A
4. Browser B harus langsung melihat:
   - Harga tertinggi berubah (dengan animasi kuning)
   - Dropdown nominal bid berubah
   - Riwayat bid bertambah di paling atas

### 3. Cek Console Log
Saat ada bid baru, console harus menampilkan:
```
[BidSent] Event diterima: {price: 420000}
[BidSent] Updating highestPrice to: 420000
[BidSent] Updating dropdown options
[BidSent] ✓ UI berhasil diupdate dengan harga: 420000
[MessageSent] Bid baru diterima: {user: {...}, bid: 420000, ...}
```

## Troubleshooting

### Jika tidak ada log `[Pusher] Connected to WebSocket`:
1. Pastikan `.env` memiliki konfigurasi Pusher yang benar:
   ```
   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=...
   PUSHER_APP_KEY=...
   PUSHER_APP_SECRET=...
   PUSHER_APP_CLUSTER=...
   ```

2. Jalankan ulang:
   ```bash
   npm run dev
   php artisan config:clear
   php artisan cache:clear
   ```

### Jika WebSocket connected tapi event tidak diterima:
1. Cek apakah event di-broadcast:
   - Lihat log Laravel di `storage/logs/laravel.log`
   - Cek Pusher dashboard untuk melihat event yang dikirim

2. Pastikan user sudah login (Echo hanya bekerja untuk authenticated user)

3. Cek channel authorization di `routes/channels.php`

### Jika error "Echo is not defined":
1. Pastikan `resources/js/bootstrap.js` di-load dengan benar
2. Cek apakah ada error saat compile `npm run dev`
3. Clear cache browser (Ctrl+Shift+Del)

### Jika queue digunakan:
Jika `QUEUE_CONNECTION=database/redis`, jalankan queue worker:
```bash
php artisan queue:work
```

## Monitoring Real-time di Pusher Dashboard
1. Login ke https://dashboard.pusher.com
2. Pilih aplikasi Anda
3. Buka tab "Debug Console"
4. Lakukan bid dan lihat event yang dikirim real-time

## Expected Behavior
✅ Bid masuk → Backend broadcast event → Frontend terima event → UI update tanpa reload
✅ Harga tertinggi update otomatis
✅ Dropdown bid update otomatis
✅ Riwayat bid bertambah di atas
✅ Semua user yang melihat halaman yang sama menerima update bersamaan
