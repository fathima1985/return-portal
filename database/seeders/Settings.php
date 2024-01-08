<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use File;

class Settings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'option_name' => 'limit_return_period',
            'option_value' => 1,
           
        ],
        [
            'option_name' => 'retutn_limit',
            'option_value' => 30,
           
        ]);
    }
}
