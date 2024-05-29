<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_rab', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_rab', 'file_lpj')) {
                $table->string('file_lpj')->nullable();
            }
            if (!Schema::hasColumn('tb_rab', 'id_proker')) {
                $table->unsignedBigInteger('id_proker')->nullable();
                $table->foreign('id_proker')->references('id_proker')->on('tb_proker');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_rab', function (Blueprint $table) {
            if (Schema::hasColumn('tb_rab', 'file_lpj')) {
                $table->dropColumn('file_lpj');
            }
            if (Schema::hasColumn('tb_rab', 'id_proker')) {
                $table->dropForeign(['id_proker']);
                $table->dropColumn('id_proker');
            }
        });
    }
};
