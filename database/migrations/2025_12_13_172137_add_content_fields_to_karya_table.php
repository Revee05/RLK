<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentFieldsToKaryaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->text('art_projects')->nullable()->after('description');
            $table->text('achievement')->nullable()->after('art_projects');
            $table->text('exhibition')->nullable()->after('achievement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('karya', function (Blueprint $table) {
            $table->dropColumn(['art_projects', 'achievement', 'exhibition']);
        });
    }
}
