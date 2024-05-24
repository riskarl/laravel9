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
        Schema::create('tb_proker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nama_organisasi');
            $table->foreignId('proposal')->nullable();
            $table->foreignId('lpj')->nullable();
            $table->string('nama_proker');
            $table->string('nama_ketupel');
            $table->date('tanggal');
            $table->string('tempat');
            $table->integer('dana_diajukan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
