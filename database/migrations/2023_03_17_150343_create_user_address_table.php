<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->index();
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->integer('provinsi_id')->index();
            $table->integer('kabupaten_id')->index();
            $table->bigInteger('kecamatan_id')->index();
            $table->bigInteger('desa_id')->index()->nullable();
            $table->bigInteger('kodepos')->nullable();
            $table->enum('label_address',['rumah','apartemen','kantor','kos'])->default('rumah');
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
        Schema::dropIfExists('user_address');
    }
}
