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
            // Menambahkan kolom file_rab jika belum ada
            if (!Schema::hasColumn('tb_rab', 'file_rab')) {
                $table->string('file_rab')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_rab', function (Blueprint $table) {
            if (Schema::hasColumn('tb_rab', 'file_rab')) {
                $table->dropColumn('file_rab');
            }
        });
    }
};
