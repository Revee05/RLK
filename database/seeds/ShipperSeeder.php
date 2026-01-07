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
        // Use delete() instead of truncate() to avoid foreign key constraint errors
        DB::table('shipper')->delete();

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
