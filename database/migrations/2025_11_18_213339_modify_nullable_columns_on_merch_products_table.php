<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyNullableColumnsOnMerchProductsTable extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE merch_products MODIFY price DECIMAL(15,2) NULL');
        DB::statement('ALTER TABLE merch_products MODIFY stock INT NULL');
        DB::statement('ALTER TABLE merch_products MODIFY discount DECIMAL(5,2) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE merch_products MODIFY price DECIMAL(15,2) NOT NULL');
        DB::statement('ALTER TABLE merch_products MODIFY stock INT NOT NULL');
        DB::statement('ALTER TABLE merch_products MODIFY discount DECIMAL(5,2) NOT NULL');
    }
}