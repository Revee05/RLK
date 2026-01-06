<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnifiedFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Payment Info (dari Xendit) - setelah snap_token
            $table->string('payment_method')->nullable()->after('snap_token');
            $table->string('payment_channel')->nullable()->after('payment_method');
            $table->string('payment_destination')->nullable()->after('payment_channel');
            $table->boolean('stock_reduced')->default(false)->after('payment_destination');
            $table->boolean('email_sent')->default(false)->after('stock_reduced');
            $table->string('payment_url')->nullable()->after('email_sent');
            $table->timestamp('paid_at')->nullable()->after('payment_url');
            $table->text('note')->nullable()->after('paid_at');
            
            // Shipping & Address (Unified) - setelah user_id
            $table->unsignedBigInteger('address_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('shipper_id')->nullable()->after('pengirim');
            $table->json('items')->nullable()->after('product_id'); // Multi-item support
            $table->json('shipping')->nullable()->after('jenis_ongkir');
            $table->boolean('gift_wrap')->default(false)->after('shipping');
            
            // Status Unified (string) - setelah order_invoice
            $table->string('status')->default('pending')->after('order_invoice'); // pending|success|expired|cancelled
            $table->string('invoice')->unique()->nullable()->after('status'); // INV-XXXXXXXXXX
            
            // Foreign Keys
            $table->foreign('address_id')->references('id')->on('user_address')->onDelete('set null');
            $table->foreign('shipper_id')->references('id')->on('shipper')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['address_id']);
            $table->dropForeign(['shipper_id']);
            
            // Drop columns
            $table->dropColumn([
                'payment_method',
                'payment_channel',
                'payment_destination',
                'stock_reduced',
                'email_sent',
                'payment_url',
                'paid_at',
                'note',
                'address_id',
                'shipper_id',
                'items',
                'shipping',
                'gift_wrap',
                'status',
                'invoice',
            ]);
        });
    }
}
