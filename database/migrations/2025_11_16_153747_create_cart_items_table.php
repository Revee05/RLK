<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Siapa yang menang lelang
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Produk apa yang dimenangkan
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Berapa jumlahnya (pasti 1 untuk lelang)
            $table->integer('quantity')->default(1); 
            
            // Berapa harga finalnya (diambil dari tabel 'bid')
            $table->decimal('price', 15, 2); 
            
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
        Schema::dropIfExists('cart_items');
    }
}
