<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockPriceDiscountToMerchProductVariants extends Migration
{
    public function up()
    {
        Schema::table('merch_product_variants', function (Blueprint $table) {
            $table->integer('stock')->nullable()->after('is_default');
            $table->decimal('price', 15, 2)->nullable()->after('stock');
            $table->decimal('discount', 5, 2)->nullable()->after('price');
        });
    }

    public function down()
    {
        Schema::table('merch_product_variants', function (Blueprint $table) {
            $table->dropColumn(['stock', 'price', 'discount']);
        });
    }
}