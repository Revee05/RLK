<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchProductImagesTable extends Migration
{
    public function up()
    {
        Schema::create('merch_product_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merch_product_id');
            $table->string('image_path');
            $table->string('label')->nullable(); // opsional: cover, detail, dsb
            $table->timestamps();

            $table->foreign('merch_product_id')->references('id')->on('merch_products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merch_product_images');
    }
}