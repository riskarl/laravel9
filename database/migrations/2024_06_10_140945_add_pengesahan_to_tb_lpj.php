<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tb_lpj', function (Blueprint $table) {
            $table->string('pengesahan')->nullable();  // This adds the 'pengesahan' column after the 'catatan' column.
        });
    }

    public function down()
    {
        Schema::table('tb_lpj', function (Blueprint $table) {
            $table->dropColumn('pengesahan');  // This will remove the column if the migration is rolled back.
        });
    }
};
