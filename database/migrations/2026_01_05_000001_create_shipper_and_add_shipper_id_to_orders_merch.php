<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateShipperAndAddShipperIdToOrdersMerch extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('shipper')) {
            Schema::create('shipper', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code', 50)->nullable()->unique();
                $table->timestamps();
            });
        }

        // Seed default shippers if none exist
        if (Schema::hasTable('shipper') && DB::table('shipper')->count() === 0) {
            DB::table('shipper')->insert([
                ['id'=>1,'name'=>'J&T Express','code'=>'jnt','created_at'=>null,'updated_at'=>null],
                ['id'=>2,'name'=>'JNE Express','code'=>'jne','created_at'=>null,'updated_at'=>null],
                ['id'=>3,'name'=>'Pos Indonesia','code'=>'pos','created_at'=>null,'updated_at'=>null],
                ['id'=>4,'name'=>'AnterAja','code'=>'anteraja','created_at'=>null,'updated_at'=>null],
                ['id'=>5,'name'=>'TIKI','code'=>'tiki','created_at'=>null,'updated_at'=>null],
                ['id'=>6,'name'=>'SiCepat','code'=>'sicepat','created_at'=>null,'updated_at'=>null],
                ['id'=>7,'name'=>'ID Express','code'=>'ide','created_at'=>null,'updated_at'=>null],
                ['id'=>8,'name'=>'SAP Express','code'=>'sap','created_at'=>null,'updated_at'=>null],
                ['id'=>9,'name'=>'Ninja Xpress','code'=>'ninja','created_at'=>null,'updated_at'=>null],
                ['id'=>10,'name'=>'Lion Parcel','code'=>'lion','created_at'=>null,'updated_at'=>null],
                ['id'=>11,'name'=>'NCS','code'=>'ncs','created_at'=>null,'updated_at'=>null],
                ['id'=>12,'name'=>'REX Express','code'=>'rex','created_at'=>null,'updated_at'=>null],
                ['id'=>13,'name'=>'RPX','code'=>'rpx','created_at'=>null,'updated_at'=>null],
                ['id'=>14,'name'=>'Sentral Cargo','code'=>'sentral','created_at'=>null,'updated_at'=>null],
                ['id'=>15,'name'=>'Star Cargo','code'=>'star','created_at'=>null,'updated_at'=>null],
                ['id'=>16,'name'=>'Wahana','code'=>'wahana','created_at'=>null,'updated_at'=>null],
                ['id'=>17,'name'=>'DSE Cargo','code'=>'dse','created_at'=>null,'updated_at'=>null],
            ]);
            // Ensure auto-increment next value
            DB::statement('ALTER TABLE shipper AUTO_INCREMENT = 18');
        }

        // Re-add shipper_id to orders_merch and foreign key
        if (Schema::hasTable('orders_merch') && ! Schema::hasColumn('orders_merch', 'shipper_id')) {
            Schema::table('orders_merch', function (Blueprint $table) {
                $table->unsignedBigInteger('shipper_id')->nullable()->after('address_id');
            });
            Schema::table('orders_merch', function (Blueprint $table) {
                $table->foreign('shipper_id')->references('id')->on('shipper')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('orders_merch') && Schema::hasColumn('orders_merch', 'shipper_id')) {
            Schema::table('orders_merch', function (Blueprint $table) {
                try { $table->dropForeign(['shipper_id']); } catch (\Exception $e) {}
                $table->dropColumn('shipper_id');
            });
        }

        Schema::dropIfExists('shipper');
    }
}
