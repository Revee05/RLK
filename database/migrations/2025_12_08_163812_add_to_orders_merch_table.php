<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToOrdersMerchTable extends Migration
{
    public function up()
    {
        Schema::table('orders_merch', function (Blueprint $table) {
            $table->json('shipping')->nullable()->after('items'); 
            $table->boolean('gift_wrap')->default(false)->after('shipping');
        });
    }

    public function down()
    {
        Schema::table('orders_merch', function (Blueprint $table) {
            $table->dropColumn('shipping');
            $table->dropColumn('gift_wrap');
        });
    }
}
