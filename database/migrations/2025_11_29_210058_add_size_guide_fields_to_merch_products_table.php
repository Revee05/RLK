<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSizeGuideFieldsToMerchProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merch_products', function (Blueprint $table) {
            $table->text('size_guide_content')->nullable()->after('description');
            $table->string('size_guide_image')->nullable()->after('size_guide_content');
            $table->string('guide_button_label')->nullable()->after('size_guide_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merch_products', function (Blueprint $table) {
            $table->dropColumn(['size_guide_content', 'size_guide_image', 'guide_button_label']);
        });
    }
}
