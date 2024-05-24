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
    Schema::table('tb_proker', function (Blueprint $table) {
        $table->dropColumn('proposal');
    });

    Schema::table('tb_proposal', function (Blueprint $table) {
        $table->foreignId('id_proker')->after('id')->nullable()->constrained('tb_proker')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('tb_proker', function (Blueprint $table) {
        $table->foreignId('proposal')->nullable();
    });

    Schema::table('tb_proposal', function (Blueprint $table) {
        $table->dropConstrainedForeignId('id_proker');
    });
}

};