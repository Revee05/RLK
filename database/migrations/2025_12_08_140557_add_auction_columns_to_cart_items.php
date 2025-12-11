<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuctionColumnsToCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // 1. Kolom ID untuk produk lelang (Nullable, karena kalau beli merch ini kosong)
            $table->unsignedBigInteger('product_id')->nullable()->after('user_id');
            
            // 2. Kolom Type (Saran temanmu)
            // Default 'merch' agar data lama aman. Isinya bisa 'merch' atau 'lelang'
            $table->string('type')->default('merch')->after('quantity'); 

            // 3. Kolom Expired khusus Cart (Ide kamu)
            // Kapan item ini harus dihapus otomatis
            $table->dateTime('expires_at')->nullable()->after('type');

            // (Opsional) Foreign key biar aman
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'type', 'expires_at']);
        });
    }
}
