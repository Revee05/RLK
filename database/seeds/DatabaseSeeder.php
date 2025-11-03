compos<?php

use Illuminate\Database\Seeder;
use App\Order;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(KategoriSeeder::class);
        $this->call(KaryaSeeder::class);
        $this->call(ShipperSeeder::class);
        $this->call(ProdukSeeder::class);
        $this->call(KelengkapanSeeder::class);
        // import provinsi
        $provinsi = base_path('database/seeds/provinsi.sql');
        DB::unprepared(file_get_contents($provinsi));
        // import kabupaten
        $kabupaten = base_path('database/seeds/kabupaten.sql');
        DB::unprepared(file_get_contents($kabupaten));
        // import kecamatan
        $kecamatan = base_path('database/seeds/kecamatan.sql');
        DB::unprepared(file_get_contents($kecamatan));

         // factory(App\Order::class, 10)->create();
        // Order::factory()->count(3)->create();

        
    }

}
