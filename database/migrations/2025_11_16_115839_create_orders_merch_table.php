<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersMerchTable extends Migration
{
    public function up()
    {
        Schema::create('orders_merch', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();

            // Alamat
            $table->unsignedBigInteger('address_id')->nullable();

            // Produk dalam CO
            $table->json('items'); // [{product_id, name, price, qty, subtotal}]

            // Pengiriman
            $table->unsignedBigInteger('shipper_id')->nullable();
            $table->string('jenis_ongkir')->nullable();
            $table->integer('total_ongkir')->default(0);

            // Pembayaran
            $table->integer('total_tagihan')->default(0);
            $table->string('invoice')->unique();
            $table->string('status')->default('pending'); // pending | paid | canceled
            $table->string('snap_token')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('address_id')->references('id')->on('user_address')->onDelete('set null');
            $table->foreign('shipper_id')->references('id')->on('shipper')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders_merch');
    }
}
