<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogImagesTable extends Migration
{
    public function up()
    {
        Schema::create('blog_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id');
            $table->string('filename');
            $table->timestamps();

            // Foreign key ke tabel posts
            $table->foreign('post_id')
                ->references('id')
                ->on('posts')
                ->onDelete('cascade'); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('blog_images');
    }
}
