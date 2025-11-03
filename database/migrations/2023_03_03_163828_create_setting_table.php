<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->text('tagline')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('wa')->nullable();
            $table->string('email')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->text('social')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting');
    }
}
