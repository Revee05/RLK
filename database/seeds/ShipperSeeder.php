<?php

use Illuminate\Database\Seeder;

class ShipperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipper')->truncate();

            $data = [
                [
                    'name' => 'j&t',
                ],
                [
                    'name' => 'jne',
                ],
                [
                    'name' => 'pos indonesia',
                ],
                [
                    'name' => 'anteraja',
                ],
                 
            ];
            DB::table('shipper')->insert($data);
    }
}
