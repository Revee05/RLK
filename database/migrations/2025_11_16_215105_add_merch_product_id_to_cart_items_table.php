<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMerchProductIdToCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Tambahkan kolom baru, boleh NULL
            $table->unsignedBigInteger('merch_product_id')->nullable()->after('product_id');

            // Buat foreign key ke tabel 'merch_products'
            $table->foreign('merch_product_id')
                  ->references('id')
                  ->on('merch_products')
                  ->onDelete('set null'); // 'set null' agar jika merch dihapus, keranjang tidak ikut hilang
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
            // Hapus foreign key dulu
            $table->dropForeign('cart_items_merch_product_id_foreign');
            // Hapus kolomnya
            $table->dropColumn('merch_product_id');
        });
    }
}
