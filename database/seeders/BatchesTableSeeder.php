<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BatchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('batches')->insert([
            ['name' =>'cse-16'],
            ['name' => 'bba-12'],
            ['name' => 'phy-113'],
            ['name' => 'bba-18'],
        ]);
    }
}
