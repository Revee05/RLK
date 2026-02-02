<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBannerNotificationTable extends Migration
{
    public function up()
    {
        Schema::create('user_banner_notification', function (Blueprint $table) {
            $table->bigIncrements('id'); 

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('type'); // winner / loser
            $table->string('title');
            $table->integer('price')->nullable();
            $table->string('checkout_url')->nullable();
            $table->boolean('is_read')->default(0); 
            $table->timestamps();

            $table->index(['user_id','is_read']);

            // Foreign key (opsional tapi disarankan)
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_banner_notification');
    }
}
