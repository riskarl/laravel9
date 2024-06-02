<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tb_lpj', function (Blueprint $table) {
            // Cek apakah kolom id_proker sudah ada
            if (!Schema::hasColumn('tb_lpj', 'id_proker')) {
                $table->bigInteger('id_proker')->unsigned()->nullable()->after('id');
            }
        });

        Schema::table('tb_rab', function (Blueprint $table) {
            // Cek apakah kolom id_proker sudah ada
            if (!Schema::hasColumn('tb_rab', 'id_proker')) {
                $table->bigInteger('id_proker')->unsigned()->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tb_lpj', function (Blueprint $table) {
            // Menghapus kolom id_proker jika ada
            if (Schema::hasColumn('tb_lpj', 'id_proker')) {
                $table->dropColumn('id_proker');
            }
        });

        Schema::table('tb_rab', function (Blueprint $table) {
            // Menghapus kolom id_proker jika ada
            if (Schema::hasColumn('tb_rab', 'id_proker')) {
                $table->dropColumn('id_proker');
            }
        });
    }
};
