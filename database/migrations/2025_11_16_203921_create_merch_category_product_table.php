<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchCategoryProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merch_category_product', function (Blueprint $table) {
            $table->bigInteger('merch_product_id')->unsigned();
            $table->bigInteger('merch_category_id')->unsigned();

            // Kunci primer gabungan
            $table->primary(['merch_product_id', 'merch_category_id'], 'merch_category_product_primary');
            
            // Kunci asing ke merch_products
            $table->foreign('merch_product_id')
                  ->references('id')
                  ->on('merch_products')
                  ->onDelete('cascade');
            
            // Kunci asing ke merch_categories
            $table->foreign('merch_category_id')
                  ->references('id')
                  ->on('merch_categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merch_category_product');
    }
}
