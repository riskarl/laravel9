<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTglMulaiPeriodeToSetAnggaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('set_anggaran', function (Blueprint $table) {
            $table->date('tgl_mulai_periode')->after('total_periode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('set_anggaran', function (Blueprint $table) {
            $table->dropColumn('tgl_mulai_periode');
        });
    }
}
