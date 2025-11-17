<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merch_product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('merch_product_id')->unsigned();
            $table->string('image_path');
            $table->string('label')->nullable();
            $table->timestamps();

            // Kunci asing ke tabel merch_products
            $table->foreign('merch_product_id')
                  ->references('id')
                  ->on('merch_products')
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
        Schema::dropIfExists('merch_product_images');
    }
}
