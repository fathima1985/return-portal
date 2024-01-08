<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use File;

class LanguageDesc extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        $code = 'en';
        if(File::exists(base_path('resources/lang/'.$code.'.json'))){
            $jsonString = file_get_contents(base_path('resources/lang/'.$code.'.json'));
            $jsonString = json_decode($jsonString, true);           
            foreach($jsonString as $key => $desc){    
                
                if(is_array($desc)){
                    $desc = json_encode($desc);
                }
                DB::table('language_keys')->insert([
                    'language_index' => $key,
                    'description' => $desc
                ]);
            }
        }
    }
}
