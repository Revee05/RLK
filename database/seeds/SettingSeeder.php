<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('setting')->truncate();

        $data = [
                'title' => 'Toko Lelang',
                'tagline' => 'Toko Accecoris terlengkap di Pati',
                'address' => 'Jl. Raya Pesanggrahan No. 20 Kec. Kesugihan Kab. Cilacap - Jawa Tengah - 53274',
                'phone' => '089282911202',
                'wa' => '089282911291',
                'email' => 'lelang@gmail.com',              
        ];

        DB::table('setting')->insert($data);
    }
}
