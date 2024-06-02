<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_proker', function (Blueprint $table) {
            $table->string('nim_ketupel')->after('nama_ketupel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_proker', function (Blueprint $table) {
            $table->dropColumn('nim_ketupel');
        });
    }
};
