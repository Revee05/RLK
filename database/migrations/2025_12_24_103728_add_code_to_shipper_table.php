<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeToShipperTable extends Migration
{
    public function up()
    {
        Schema::table('shipper', function (Blueprint $table) {
            $table->string('code', 50)
                  ->nullable()
                  ->after('name')
                  ->comment('Courier code untuk API eksternal (jne, tiki, pos, dll)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipper', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
