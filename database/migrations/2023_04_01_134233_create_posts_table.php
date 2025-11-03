<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index();
            $table->integer('kategori_id')->index()->default('0');
            $table->string('image')->nullable();
            $table->string('slug');
            $table->string('title');
            $table->text('body');
            $table->enum('status', ['PUBLISHED', 'DRAFT', 'PENDING'])->default('DRAFT');
            $table->dateTime('publish_date')->nullable();
            $table->string('post_type')->default('posts');
            $table->string('views')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
