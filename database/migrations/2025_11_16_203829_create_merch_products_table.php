<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merch_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('price');
            $table->integer('stock')->default(0);
            
            $table->bigInteger('category_id')->unsigned()->nullable(); 
            
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('type', ['normal', 'featured'])->default('normal');
            $table->timestamps();
            $table->integer('discount')->default(0);

            // Menambahkan semua index
            $table->index('name', 'idx_name');
            $table->index('price', 'idx_price');
            $table->index('status', 'idx_status');
            $table->index('type', 'idx_type');
            $table->index('category_id', 'idx_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merch_products');
    }
}
