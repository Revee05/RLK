<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVariantColumnsToCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
        // Tambah kolom variant (nullable) setelah merch_product_id
        $table->unsignedBigInteger('merch_product_variant_id')->nullable()->after('merch_product_id');
        
        // Tambah kolom size (nullable) setelah variant
        $table->unsignedBigInteger('merch_product_variant_size_id')->nullable()->after('merch_product_variant_id');
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
        $table->dropColumn(['merch_product_variant_id', 'merch_product_variant_size_id']);
    });
    }
}
