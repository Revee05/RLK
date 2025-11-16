<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchCategoriesTable extends Migration
{
    public function up()
    {
        // --- merch_categories ---
        Schema::create('merch_categories', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('slug')->unique();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Index sesuai DDL
            $table->index('name', 'idx_name');
        });

        // --- merch_category_product (pivot) ---
        Schema::create('merch_category_product', function (Blueprint $table) {
            $table->unsignedBigInteger('merch_product_id');
            $table->unsignedBigInteger('merch_category_id');

            // Primary key sesuai DDL
            $table->primary(
                ['merch_product_id', 'merch_category_id'],
                'merch_category_product_primary'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('merch_category_product');
        Schema::dropIfExists('merch_categories');
    }
}
