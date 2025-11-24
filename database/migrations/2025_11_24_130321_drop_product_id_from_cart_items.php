<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProductIdFromCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // 1. Hapus Foreign Key Constraint-nya dulu
            // Kita pakai array syntax, Laravel akan otomatis mencari nama key: 'cart_items_product_id_foreign'
            $table->dropForeign(['product_id']); 

            // 2. Baru hapus kolomnya
            $table->dropColumn('product_id');
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
            // Kembalikan kolomnya dulu
            $table->unsignedBigInteger('product_id')->nullable()->after('user_id');

            // Kembalikan Foreign Key-nya (sesuaikan 'products' jika nama tabel produkmu beda)
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
