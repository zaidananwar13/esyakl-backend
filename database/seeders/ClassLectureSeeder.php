<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClassLectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_lecture')->insert([
            'id_class' => 5,
            'id_user' => 20,
            'type' => 'undefined',
            'status' => 'undefined',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('class_lecture')->insert([
            'id_class' => 9,
            'id_user' => 55,
            'type' => 'undefined',
            'status' => 'undefined',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
