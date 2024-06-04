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
        Schema::table('tb_rab', function (Blueprint $table) {
            $table->string('file_srpd')->nullable()->after('file_rab');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_rab', function (Blueprint $table) {
            $table->dropColumn('file_srpd');
        });
    }
};
