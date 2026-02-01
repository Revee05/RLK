<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamMembersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('team_members', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->string('role');
      $table->string('email')->nullable();
      $table->string('instagram')->nullable();
      $table->string('avatar')->nullable();
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
    Schema::dropIfExists('team_members');
  }
}
