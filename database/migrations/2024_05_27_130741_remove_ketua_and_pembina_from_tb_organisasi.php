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
        Schema::table('tb_organisasi', function (Blueprint $table) {
            $table->dropColumn('nama_ketua');
            $table->dropColumn('nama_pembina');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_organisasi', function (Blueprint $table) {
            $table->string('nama_ketua');
            $table->string('nama_pembina');
        });
    }
};
