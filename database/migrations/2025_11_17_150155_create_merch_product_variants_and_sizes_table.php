<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchProductVariantsAndSizesTable extends Migration
{
    public function up()
    {
        // merch_product_variants
        Schema::create('merch_product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merch_product_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();

            $table->foreign('merch_product_id')->references('id')->on('merch_products')->onDelete('cascade');
        });

        // merch_product_variant_images
        Schema::create('merch_product_variant_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merch_product_variant_id');
            $table->string('image_path');
            $table->string('label')->nullable();
            $table->timestamps();

            $table->foreign('merch_product_variant_id')->references('id')->on('merch_product_variants')->onDelete('cascade');
        });

        // merch_product_variant_sizes
        Schema::create('merch_product_variant_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('merch_product_variant_id');
            $table->string('size');
            $table->integer('stock')->default(0);
            $table->integer('price')->nullable();
            $table->integer('discount')->default(0);
            $table->timestamps();

            $table->foreign('merch_product_variant_id')->references('id')->on('merch_product_variants')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('merch_product_variant_sizes');
        Schema::dropIfExists('merch_product_variant_images');
        Schema::dropIfExists('merch_product_variants');
    }
}