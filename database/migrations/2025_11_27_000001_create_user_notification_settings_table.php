<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            // Email
            $table->boolean('email_enabled')->default(true);
            $table->boolean('email_order_status')->default(true);
            $table->boolean('email_promo')->default(true);
            // WhatsApp
            $table->boolean('wa_enabled')->default(false);
            $table->boolean('wa_order_status')->default(false);
            $table->boolean('wa_promo')->default(false);
            // Banner
            $table->boolean('banner_enabled')->default(true);
            $table->boolean('banner_order_status')->default(true);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_notification_settings');
    }
}
