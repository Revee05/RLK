<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyUserAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: This migration uses ->change() to alter column type. Ensure
     * the package "doctrine/dbal" is installed: `composer require doctrine/dbal`
     *
     * @return void
     */
    public function up()
    {
        // Pastikan doctrine/dbal ^2 terinstall sebelum menjalankan migration ini.
        // Daftarkan mapping enum -> string supaya ->change() tidak error pada DBAL
        $connection = Schema::getConnection();
        try {
            $platform = $connection->getDoctrineSchemaManager()->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('enum', 'string');
        } catch (\Throwable $e) {
            // jika gagal (langkah defensif), lanjutkan ke ALTER raw SQL
        }

        // Coba ubah tipe memakai change() (setelah mapping)
        try {
            Schema::table('user_address', function (Blueprint $table) {
                $table->string('label_address')->nullable()->change();
            });
        } catch (\Throwable $e) {
            // Fallback: gunakan raw SQL untuk mengubah kolom jika DBAL masih bermasalah
            DB::statement("ALTER TABLE `user_address` MODIFY `label_address` VARCHAR(255) NULL");
        }

        // Tambah kolom is_primary jika belum ada
        if (!Schema::hasColumn('user_address', 'is_primary')) {
            Schema::table('user_address', function (Blueprint $table) {
                $table->boolean('is_primary')->default(false)->after('label_address')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Hapus kolom is_primary jika ada
        if (Schema::hasColumn('user_address', 'is_primary')) {
            Schema::table('user_address', function (Blueprint $table) {
                $table->dropIndex(['is_primary']);
                $table->dropColumn('is_primary');
            });
        }

        // Anda bisa menyesuaikan down() agar kembali ke enum jika diperlukan.
        // Untuk safety, kita ubah kembali ke VARCHAR (tidak mengembalikan enum lama)
        try {
            Schema::table('user_address', function (Blueprint $table) {
                $table->string('label_address')->nullable()->change();
            });
        } catch (\Throwable $e) {
            DB::statement("ALTER TABLE `user_address` MODIFY `label_address` VARCHAR(255) NULL");
        }
    }
}
