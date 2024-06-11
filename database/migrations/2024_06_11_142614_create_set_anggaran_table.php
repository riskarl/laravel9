<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetAnggaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_anggaran', function (Blueprint $table) {
            $table->id('id_set_anggaran');
            $table->decimal('total_anggaran', 15, 2); // Bisa diubah sesuai kebutuhan presisi
            $table->enum('jenis_periode', ['bulan', 'tahun']);
            $table->integer('total_periode');
            $table->timestamps(); // ini akan otomatis membuat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('set_anggaran');
    }
}
