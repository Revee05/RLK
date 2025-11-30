<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserAddressRegionColumns extends Migration
{
    public function up()
    {
        Schema::table('user_address', function (Blueprint $table) {

            //Tambah kolom baru
            $table->integer('province_id')->index()->nullable();
            $table->integer('city_id')->index()->nullable();
            $table->bigInteger('district_id')->index()->nullable();
        });

        //Copy isi dari kolom lama -> kolom baru
        DB::table('user_address')->update([
            'province_id' => DB::raw('provinsi_id'),
            'city_id'     => DB::raw('kabupaten_id'),
            'district_id' => DB::raw('kecamatan_id'),
        ]);

        Schema::table('user_address', function (Blueprint $table) {

            //Hapus kolom lama
            $table->dropColumn([
                'provinsi_id',
                'kabupaten_id',
                'kecamatan_id',
                'desa_id',
            ]);
        });
    }

    public function down()
    {
        Schema::table('user_address', function (Blueprint $table) {

            // Buat kembali kolom lama
            $table->integer('provinsi_id')->nullable();
            $table->integer('kabupaten_id')->nullable();
            $table->bigInteger('kecamatan_id')->nullable();
            $table->bigInteger('desa_id')->nullable();
        });

        // Copy balik kolom baru -> kolom lama
        DB::table('user_address')->update([
            'provinsi_id' => DB::raw('province_id'),
            'kabupaten_id'=> DB::raw('city_id'),
            'kecamatan_id'=> DB::raw('district_id'),
        ]);

        // Hapus kolom baru
        Schema::table('user_address', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'city_id', 'district_id']);
        });
    }
}
