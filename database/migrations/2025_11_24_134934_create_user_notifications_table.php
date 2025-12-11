<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            // User
            $table->unsignedBigInteger('user_id');

            // User preference (default ON)
            $table->boolean('notif_via_web')->default(1);
            $table->boolean('notif_via_email')->default(1);
            $table->boolean('notif_via_whatsapp')->default(1);

            // Notification payload (nullable)
            $table->string('type')->nullable(); // winner/loser
            $table->string('title')->nullable();
            $table->integer('price')->nullable();
            $table->string('checkout_url')->nullable();

            // fallback unread
            $table->boolean('is_read')->default(0);
            $table->timestamps();

            // relation
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notifications');
    }
}
