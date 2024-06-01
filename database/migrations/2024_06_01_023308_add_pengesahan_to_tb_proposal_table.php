<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPengesahanToTbProposalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_proposal', function (Blueprint $table) {
            $table->string('pengesahan')->nullable(); // atau tipe data lain sesuai kebutuhan
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_proposal', function (Blueprint $table) {
            $table->dropColumn('pengesahan');
        });
    }
}
