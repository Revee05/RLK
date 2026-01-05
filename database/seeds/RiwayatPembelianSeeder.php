<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\OrderMerch;
use App\Order;
use App\User;
use App\UserAddress;
use App\Shipper;
use Carbon\Carbon;

class RiwayatPembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get existing user (Rifky)
        $user = User::where('id', 6894)->orWhere('email', 'alfarezt05@gmail.com')->first();
        
        if (!$user) {
            $this->command->error('User Rifky (ID: 6894 / alfarezt05@gmail.com) not found. Please make sure the user exists.');
            return;
        }

        // Get or create address
        $address = UserAddress::where('user_id', $user->id)->first();
        if (!$address) {
            $address = UserAddress::create([
                'user_id' => $user->id,
                'name' => 'Rifky',
                'phone' => '081234567890',
                'label_address' => 'rumah',
                'address' => 'Jl. Merdeka No. 123, RT 02/RW 05',
                'province_id' => 1,
                'city_id' => 10,
                'district_id' => 100,
                'kodepos' => 40132,
            ]);
        }

        // Get or create shipper
        $shipper = Shipper::first();
        if (!$shipper) {
            $shipper = Shipper::create([
                'name' => 'JNE',
                'code' => 'jne',
            ]);
        }

        // Seed Merchandise Orders
        $this->seedMerchOrders($user, $address, $shipper);

        // Seed Auction Orders (if you have lelang/auction system)
        $this->seedLelangOrders($user);

        $this->command->info('Riwayat pembelian seeder completed successfully!');
    }

    private function seedMerchOrders($user, $address, $shipper)
    {
        // Order 1 - Completed
        OrderMerch::create([
            'user_id' => $user->id,
            'invoice' => 'INV-YGVBKT0UM3',
            'address_id' => $address->id,
            'shipper_id' => $shipper->id,
            'jenis_ongkir' => 'Regular',
            'total_ongkir' => 25000,
            'total_tagihan' => 345000,
            'status' => 'completed',
            'note' => 'Mohon dipacking rapi, untuk hadiah',
            'items' => json_encode([
                [
                    'name' => 'Kemeja Flannel Kotak-kotak (L)',
                    'price' => 150000,
                    'quantity' => 2,
                    'image' => '/uploads/products/kemeja-flannel.jpg'
                ],
                [
                    'name' => 'Kaos Polos Hitam (M)',
                    'price' => 45000,
                    'quantity' => 1,
                    'image' => '/uploads/products/kaos-polos.jpg'
                ]
            ]),
            'created_at' => Carbon::parse('2025-11-26 10:30:00'),
            'updated_at' => Carbon::parse('2025-11-27 15:20:00'),
        ]);

        // Order 2 - Processing (Paid)
        OrderMerch::create([
            'user_id' => $user->id,
            'invoice' => 'INV-USDGSTA0YF',
            'address_id' => $address->id,
            'shipper_id' => $shipper->id,
            'jenis_ongkir' => 'Regular',
            'total_ongkir' => 15000,
            'total_tagihan' => 230000,
            'status' => 'paid',
            'items' => json_encode([
                [
                    'name' => 'Cybor 6 From Dipolar',
                    'price' => 115000,
                    'quantity' => 2,
                    'image' => '/uploads/products/cybor-6.jpg'
                ]
            ]),
            'created_at' => Carbon::parse('2025-11-20 14:15:00'),
            'updated_at' => Carbon::parse('2025-11-20 14:45:00'),
        ]);

        // Order 3 - Cancelled
        OrderMerch::create([
            'user_id' => $user->id,
            'invoice' => 'INV-UFTYUAOYF',
            'address_id' => $address->id,
            'shipper_id' => $shipper->id,
            'jenis_ongkir' => 'Regular',
            'total_ongkir' => 20000,
            'total_tagihan' => 120000,
            'status' => 'cancelled',
            'items' => json_encode([
                [
                    'name' => 'Custom Packaging',
                    'price' => 120000,
                    'quantity' => 1,
                    'image' => '/uploads/products/custom-packaging.jpg'
                ]
            ]),
            'created_at' => Carbon::parse('2025-11-17 09:20:00'),
            'updated_at' => Carbon::parse('2025-11-17 10:30:00'),
        ]);

        // Order 4 - Pending
        OrderMerch::create([
            'user_id' => $user->id,
            'invoice' => 'INV-PENDING001',
            'address_id' => $address->id,
            'shipper_id' => $shipper->id,
            'jenis_ongkir' => 'Express',
            'total_ongkir' => 35000,
            'total_tagihan' => 285000,
            'status' => 'pending',
            'items' => json_encode([
                [
                    'name' => 'Tote Bag Canvas',
                    'price' => 75000,
                    'quantity' => 2,
                    'image' => '/uploads/products/tote-bag.jpg'
                ],
                [
                    'name' => 'Sticker Pack',
                    'price' => 25000,
                    'quantity' => 4,
                    'image' => '/uploads/products/sticker-pack.jpg'
                ]
            ]),
            'created_at' => Carbon::parse('2025-11-24 16:00:00'),
            'updated_at' => Carbon::parse('2025-11-24 16:00:00'),
        ]);

        // Order 5 - Shipped
        OrderMerch::create([
            'user_id' => $user->id,
            'invoice' => 'INV-SHIPPED001',
            'address_id' => $address->id,
            'shipper_id' => $shipper->id,
            'jenis_ongkir' => 'Regular',
            'total_ongkir' => 18000,
            'total_tagihan' => 218000,
            'status' => 'shipped',
            'items' => json_encode([
                [
                    'name' => 'Poster Art Print A3',
                    'price' => 100000,
                    'quantity' => 2,
                    'image' => '/uploads/products/poster-art.jpg'
                ]
            ]),
            'created_at' => Carbon::parse('2025-11-22 11:30:00'),
            'updated_at' => Carbon::parse('2025-11-23 09:15:00'),
        ]);
    }

    private function seedLelangOrders($user)
    {
        // Check if Order model and lelang system exists
        if (!class_exists('App\Order')) {
            $this->command->warn('Order model for lelang not found, skipping lelang orders seeding');
            return;
        }

        // Get address for order
        $address = UserAddress::where('user_id', $user->id)->first();
        if (!$address) {
            $this->command->warn('User address not found, skipping lelang orders seeding');
            return;
        }

        // Order 1 - Completed Auction (payment_status='2', status_pesanan='3')
        Order::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $address->phone ?? '081234567890',
            'label_address' => $address->label_address ?? 'rumah',
            'address' => $address->address,
            'provinsi_id' => $address->province_id ?? 1,
            'kabupaten_id' => $address->city_id ?? 10,
            'kecamatan_id' => $address->district_id ?? 100,
            'product_id' => 1, // ID karya lelang
            'pengirim' => 'JNE',
            'jenis_ongkir' => 'REG',
            'bid_terakhir' => 520000,
            'total_ongkir' => '15000',
            'asuransi_pengiriman' => 5000,
            'total_tagihan' => 540000,
            'orderid_uuid' => \Illuminate\Support\Str::uuid(),
            'order_invoice' => 'INV-LELANG001',
            'payment_status' => '2', // sudah dibayar
            'status_pesanan' => '3', // diterima
            'nomor_resi' => 'JNE123456789',
            'created_at' => Carbon::parse('2025-11-18 16:45:00'),
            'updated_at' => Carbon::parse('2025-11-19 10:30:00'),
        ]);

        // Order 2 - Pending Auction (payment_status='1', status_pesanan='1')
        Order::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $address->phone ?? '081234567890',
            'label_address' => $address->label_address ?? 'rumah',
            'address' => $address->address,
            'provinsi_id' => $address->province_id ?? 1,
            'kabupaten_id' => $address->city_id ?? 10,
            'kecamatan_id' => $address->district_id ?? 100,
            'product_id' => 2, // ID karya lelang
            'pengirim' => 'JNE',
            'jenis_ongkir' => 'REG',
            'bid_terakhir' => 750000,
            'total_ongkir' => '20000',
            'asuransi_pengiriman' => 7500,
            'total_tagihan' => 777500,
            'orderid_uuid' => \Illuminate\Support\Str::uuid(),
            'order_invoice' => 'INV-LELANG002',
            'payment_status' => '1', // menunggu pembayaran
            'status_pesanan' => '1', // belum diproses
            'created_at' => Carbon::parse('2025-11-15 11:00:00'),
            'updated_at' => Carbon::parse('2025-11-15 11:00:00'),
        ]);

        // Order 3 - Another Completed Auction
        Order::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $address->phone ?? '081234567890',
            'label_address' => $address->label_address ?? 'rumah',
            'address' => $address->address,
            'provinsi_id' => $address->province_id ?? 1,
            'kabupaten_id' => $address->city_id ?? 10,
            'kecamatan_id' => $address->district_id ?? 100,
            'product_id' => 3, // ID karya lelang
            'pengirim' => 'JNE',
            'jenis_ongkir' => 'YES',
            'bid_terakhir' => 890000,
            'total_ongkir' => '25000',
            'asuransi_pengiriman' => 8900,
            'total_tagihan' => 923900,
            'orderid_uuid' => \Illuminate\Support\Str::uuid(),
            'order_invoice' => 'INV-LELANG003',
            'payment_status' => '2', // sudah dibayar
            'status_pesanan' => '2', // dikirim
            'nomor_resi' => 'JNE987654321',
            'created_at' => Carbon::parse('2025-11-10 14:20:00'),
            'updated_at' => Carbon::parse('2025-11-11 09:00:00'),
        ]);
    }
}
