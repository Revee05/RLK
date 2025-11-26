<?php

use Illuminate\Database\Seeder;
use App\OrderMerch;
use App\User;
use App\UserAddress;
use App\Shipper;
use Illuminate\Support\Str;

class OrderMerchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cari user yang memiliki alamat
        $address = UserAddress::with('user')->first();
        
        if (!$address) {
            $this->command->error('Tidak ada alamat di database. Silakan buat alamat terlebih dahulu di halaman account.');
            return;
        }

        $user = $address->user;
        
        if (!$user) {
            $this->command->error('User tidak ditemukan.');
            return;
        }

        // Ambil shipper
        $shipper = Shipper::first();
        
        if (!$shipper) {
            $this->command->error('Tidak ada shipper di database. Silakan buat shipper terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat data dummy OrderMerch untuk user: ' . $user->name . ' (ID: ' . $user->id . ')');
        $this->command->info('');

        // Data dummy orders
        $orders = [
            // Order 1 - Status: Completed (Selesai)
            [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'items' => json_encode([
                    [
                        'id' => 1,
                        'name' => 'Hoodie Hitam Premium (L)',
                        'price' => 250000,
                        'quantity' => 2,
                        'image' => '/uploads/merch/hoodie-black.jpg',
                        'product_id' => 1,
                        'variant_id' => 1,
                        'size_id' => 3
                    ],
                    [
                        'id' => 2,
                        'name' => 'Tote Bag Canvas',
                        'price' => 75000,
                        'quantity' => 1,
                        'image' => '/uploads/merch/totebag.jpg',
                        'product_id' => 2,
                        'variant_id' => 2,
                        'size_id' => null
                    ]
                ]),
                'shipper_id' => $shipper->id,
                'jenis_ongkir' => 'Regular',
                'total_ongkir' => 15000,
                'total_tagihan' => 590000, // (250000*2) + 75000 + 15000
                'invoice' => 'INV-' . strtoupper(Str::random(10)),
                'status' => 'completed',
                'snap_token' => null,
                'note' => 'Pesanan sudah diterima dengan baik, terima kasih!',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(10),
            ],

            // Order 2 - Status: Paid (Sudah Dibayar, Sedang Diproses)
            [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'items' => json_encode([
                    [
                        'id' => 3,
                        'name' => 'Kaos Polos Putih (M)',
                        'price' => 85000,
                        'quantity' => 3,
                        'image' => '/uploads/merch/kaos-white.jpg',
                        'product_id' => 3,
                        'variant_id' => 3,
                        'size_id' => 2
                    ],
                    [
                        'id' => 4,
                        'name' => 'Sticker Pack Limited Edition',
                        'price' => 35000,
                        'quantity' => 2,
                        'image' => '/uploads/merch/sticker-pack.jpg',
                        'product_id' => 4,
                        'variant_id' => 4,
                        'size_id' => null
                    ]
                ]),
                'shipper_id' => $shipper->id,
                'jenis_ongkir' => 'Express',
                'total_ongkir' => 25000,
                'total_tagihan' => 350000, // (85000*3) + (35000*2) + 25000
                'invoice' => 'INV-' . strtoupper(Str::random(10)),
                'status' => 'paid',
                'snap_token' => 'snap_' . Str::random(32),
                'note' => 'Mohon dikirim secepatnya',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],

            // Order 3 - Status: Shipped (Sedang Dikirim)
            [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'items' => json_encode([
                    [
                        'id' => 5,
                        'name' => 'Jaket Bomber Premium (XL)',
                        'price' => 450000,
                        'quantity' => 1,
                        'image' => '/uploads/merch/jaket-bomber.jpg',
                        'product_id' => 5,
                        'variant_id' => 5,
                        'size_id' => 4
                    ]
                ]),
                'shipper_id' => $shipper->id,
                'jenis_ongkir' => 'Next Day',
                'total_ongkir' => 35000,
                'total_tagihan' => 485000, // 450000 + 35000
                'invoice' => 'INV-' . strtoupper(Str::random(10)),
                'status' => 'shipped',
                'snap_token' => 'snap_' . Str::random(32),
                'note' => 'Paket sudah dalam perjalanan',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(1),
            ],

            // Order 4 - Status: Pending (Menunggu Pembayaran)
            [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'items' => json_encode([
                    [
                        'id' => 6,
                        'name' => 'Celana Jeans Slim Fit (32)',
                        'price' => 320000,
                        'quantity' => 1,
                        'image' => '/uploads/merch/jeans.jpg',
                        'product_id' => 6,
                        'variant_id' => 6,
                        'size_id' => 5
                    ],
                    [
                        'id' => 7,
                        'name' => 'Topi Baseball Cap',
                        'price' => 95000,
                        'quantity' => 2,
                        'image' => '/uploads/merch/cap.jpg',
                        'product_id' => 7,
                        'variant_id' => 7,
                        'size_id' => null
                    ]
                ]),
                'shipper_id' => $shipper->id,
                'jenis_ongkir' => 'Regular',
                'total_ongkir' => 18000,
                'total_tagihan' => 528000, // 320000 + (95000*2) + 18000
                'invoice' => 'INV-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'snap_token' => 'snap_' . Str::random(32),
                'note' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],

            // Order 5 - Status: Completed (dengan Gift Wrap)
            [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'items' => json_encode([
                    [
                        'id' => 8,
                        'name' => 'Sweater Rajut Abu-abu (L)',
                        'price' => 185000,
                        'quantity' => 1,
                        'image' => '/uploads/merch/sweater.jpg',
                        'product_id' => 8,
                        'variant_id' => 8,
                        'size_id' => 3
                    ],
                    [
                        'id' => 9,
                        'name' => 'Tumbler Stainless 500ml',
                        'price' => 125000,
                        'quantity' => 1,
                        'image' => '/uploads/merch/tumbler.jpg',
                        'product_id' => 9,
                        'variant_id' => 9,
                        'size_id' => null
                    ]
                ]),
                'shipper_id' => $shipper->id,
                'jenis_ongkir' => 'Regular',
                'total_ongkir' => 12000,
                'total_tagihan' => 332000, // 185000 + 125000 + 12000 + 10000 (gift wrap)
                'invoice' => 'INV-' . strtoupper(Str::random(10)),
                'status' => 'completed',
                'snap_token' => null,
                'note' => 'Untuk hadiah ulang tahun ( + Gift Wrap)',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(18),
            ],

            // Order 6 - Status: Pending (Baru saja)
            [
                'user_id' => $user->id,
                'address_id' => $address->id,
                'items' => json_encode([
                    [
                        'id' => 10,
                        'name' => 'Kemeja Flannel Kotak-kotak (L)',
                        'price' => 165000,
                        'quantity' => 2,
                        'image' => '/uploads/merch/flannel.jpg',
                        'product_id' => 10,
                        'variant_id' => 10,
                        'size_id' => 3
                    ]
                ]),
                'shipper_id' => $shipper->id,
                'jenis_ongkir' => 'Regular',
                'total_ongkir' => 15000,
                'total_tagihan' => 345000, // (165000*2) + 15000
                'invoice' => 'INV-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'snap_token' => 'snap_' . Str::random(32),
                'note' => null,
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
        ];

        // Insert data
        foreach ($orders as $order) {
            OrderMerch::create($order);
            $this->command->info('✓ Order ' . $order['invoice'] . ' (' . $order['status'] . ') berhasil dibuat');
        }

        $this->command->info('');
        $this->command->info('======================================');
        $this->command->info('Seeder OrderMerch berhasil dijalankan!');
        $this->command->info('Total: ' . count($orders) . ' orders dummy');
        $this->command->info('======================================');
    }
}
