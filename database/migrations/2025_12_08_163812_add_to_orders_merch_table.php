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
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_channel')->nullable()->after('payment_method');
            $table->string('payment_destination')->nullable()->after('payment_channel');
            $table->boolean('stock_reduced')->default(false)->after('payment_destination');
            $table->boolean('email_sent')->default(0)->after('status')->comment('1 = email konfirmasi sudah dikirim'); 
        });
    }

    public function down()
    {
        Schema::table('orders_merch', function (Blueprint $table) {
            $table->dropColumn(['shipping', 'gift_wrap', 'payment_method', 'payment_channel', 'payment_destination', 'stock_reduced', 'email_sent']);
        });
    }
}
