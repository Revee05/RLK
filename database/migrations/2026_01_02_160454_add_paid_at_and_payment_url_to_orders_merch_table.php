<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAtAndPaymentUrlToOrdersMerchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_merch', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->string('payment_url')->nullable()->after('snap_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_merch', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'payment_url']);
        });
    }
}
