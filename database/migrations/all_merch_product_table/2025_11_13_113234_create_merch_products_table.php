<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchProductsTable extends Migration
{
    public function up()
    {
        Schema::create('merch_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price');
            $table->integer('stock')->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('type', ['normal', 'featured'])->default('normal');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('discount')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('merch_products');
    }
}
