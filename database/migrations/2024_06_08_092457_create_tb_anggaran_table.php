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
        Schema::create('tb_anggaran', function (Blueprint $table) {
            $table->id('id_anggaran');
            $table->unsignedBigInteger('id_organisasi');
            $table->integer('jumlah_mhs');
            $table->integer('jumlah_anggaran');
            $table->integer('total_anggaran');
            $table->timestamps();

            // Menambahkan foreign key constraints
            $table->foreign('id_organisasi')->references('id')->on('tb_organisasi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_anggaran');
    }
};
