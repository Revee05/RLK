<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            // Kolom Gambar Mobile
            $table->string('image_mobile')->nullable()->after('image');
            
            // Kolom Detail Jadwal & Lokasi (Sesuai Kotak Navy)
            $table->string('online_period')->nullable()->after('subtitle'); // Utk: "15 Nov - 31 Des"
            $table->string('offline_date')->nullable()->after('online_period'); // Utk: "01 Jan 2026"
            $table->string('location')->nullable()->after('offline_date'); // Utk: Alamat
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['image_mobile', 'online_period', 'offline_date', 'location']);
        });
    }
}
