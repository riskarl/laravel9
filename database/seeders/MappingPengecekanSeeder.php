<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MappingPengecekanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['organisasi' => 'BEM', 'flow' => '10|9|7|4|2|1'],
            ['organisasi' => 'HIMA', 'flow' => '12|11|9|7|5|3|2|1'],
            ['organisasi' => 'UKM', 'flow' => '14|13|9|7|6|2|1']
        ];

        DB::table('mapping_pengecekan')->insert($data);
    }

}
