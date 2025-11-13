<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('merch_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Pivot table untuk relasi many-to-many (jika satu produk bisa punya banyak kategori)
        Schema::create('merch_category_product', function (Blueprint $table) {
            $table->unsignedBigInteger('merch_product_id');
            $table->unsignedBigInteger('merch_category_id');
            $table->primary(['merch_product_id', 'merch_category_id'], 'merch_cat_prod_primary');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merch_category_product');
        Schema::dropIfExists('merch_categories');
    }
}