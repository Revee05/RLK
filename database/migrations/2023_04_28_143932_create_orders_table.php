<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index();
            $table->string('name');
            $table->string('phone');
            $table->string('label_address');
            $table->text('address');
            $table->integer('provinsi_id')->index();
            $table->integer('kabupaten_id')->index();
            $table->bigInteger('kecamatan_id')->index();
            $table->integer('product_id')->index();
            $table->string('pengirim');
            $table->text('jenis_ongkir');
            $table->integer('bid_terakhir')->default(0);
            $table->text('total_ongkir');
            $table->integer('asuransi_pengiriman')->default(0);
            $table->integer('total_tagihan')->default(0);
            $table->uuid('orderid_uuid');
            $table->string('order_invoice');
            $table->enum('payment_status', ['1', '2', '3', '4'])->comment('1=menunggu pembayaran, 2=sudah dibayar, 3=kadaluarsa, 4=batal')->default(1);
            $table->enum('status_pesanan', ['1', '2', '3', '4'])->comment('1=belum diproses, 2=dikirim, 3=diterima, 4=dikembalikan')->default(1);
            $table->text('nomor_resi')->nullable();
            
            $table->string('snap_token', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
