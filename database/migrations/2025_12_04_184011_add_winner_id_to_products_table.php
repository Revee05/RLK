<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWinnerIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('winner_id')->nullable()->after('status');
            // Jika ingin foreign key ke tabel users:
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Jika Anda menambahkan foreign key, drop constraint dulu:
            $table->dropForeign(['winner_id']);
            $table->dropColumn('winner_id');
        });
    }
}
