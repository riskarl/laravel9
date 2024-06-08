<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_lpj', function (Blueprint $table) {
            Schema::table('tb_lpj', function (Blueprint $table) {
                $table->integer('dana_disetujui')->nullable()->after('catatan'); // Ganti 'existing_column' dengan nama kolom yang sudah ada sebelumnya
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_lpj', function (Blueprint $table) {
            Schema::table('tb_lpj', function (Blueprint $table) {
                $table->dropColumn('dana_disetujui');
            });
        });
    }
};
