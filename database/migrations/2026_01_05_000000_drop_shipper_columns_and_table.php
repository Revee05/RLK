<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropShipperColumnsAndTable extends Migration
{
    public function up()
    {
        // Remove foreign key and column from orders_merch if present
        if (Schema::hasTable('orders_merch') && Schema::hasColumn('orders_merch', 'shipper_id')) {
            Schema::table('orders_merch', function (Blueprint $table) {
                try {
                    $table->dropForeign(['shipper_id']);
                } catch (\Exception $e) {
                    // ignore if foreign key does not exist
                }
                $table->dropColumn('shipper_id');
            });
        }

        // Drop shipper table if exists
        Schema::dropIfExists('shipper');
    }

    public function down()
    {
        // Recreate shipper table
        if (! Schema::hasTable('shipper')) {
            Schema::create('shipper', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code')->nullable()->unique();
                $table->timestamps();
            });
        }

        // Re-add shipper_id to orders_merch
        if (Schema::hasTable('orders_merch') && ! Schema::hasColumn('orders_merch', 'shipper_id')) {
            Schema::table('orders_merch', function (Blueprint $table) {
                $table->unsignedBigInteger('shipper_id')->nullable()->after('address_id');
                $table->foreign('shipper_id')->references('id')->on('shipper')->onDelete('set null');
            });
        }
    }
}
