<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;


class LearningPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('learning_path')->insert([
            'id_learning_path' => 1,
            'name' => "Nahwu Class",
            'desc' => "Language is very important and the impression that Nahwu is a difficult science to learn, especially by Muslims. As one of the basic sciences in Arabic",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('learning_path')->insert([
            'id_learning_path' => 2,
            'name' => "Shorof Class",
            'desc' => "Shorof is one of the branches of knowledge in Arabic which studies about changing the shape of a word in Arabic",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('learning_path')->insert([
            'id_learning_path' => 3,
            'name' => "Kaligrafi Class",
            'desc' => "In the book Calligraphy Art (1985) by Abdul Karim Husain, the word calligraphy comes from Latin which consists of kalios (calios) which means beautiful and graf (graph) which means drawing or writing in Arabic.",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
