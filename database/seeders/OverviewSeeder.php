<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OverviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('overview')->insert([
            'id_overview' => 1,
            'title' => "Come learn Arabic here, Go chase your dream!",
            'desc' => "The first free online learning platform that focused on Arabic. With our various excellent features, start your learning Arabic journey with us!",
            'id_bahasa' => 1,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 2,
            'title' => "Why you'll love learning with Arabic-Go?",
            'desc' => "It's time to choose learning resources wisely. Not only guaranteed materials, Arabic-Go also has professional mentors who will help you learn Arabic quickly.",
            'id_bahasa' => 1,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 3,
            'title' => "Choose our featured class",
            'desc' => "Still new in Arabic? We got you! Our learning paths will contain three classes divided by the level of knowledge. You can start with beginner class!",
            'id_bahasa' => 1,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 4,
            'title' => "Learning is easier with our giving harakat feature",
            'desc' => "Confused in reading arabic without harakat? No worries, we'll do it for you! Introducing, our giving harakat feature.",
            'id_bahasa' => 1,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 5,
            'title' => "What do they say about Arabic-Go?",
            'desc' => "The reviews posted here are their own reviews and not in any association with anyone at Arabic-Go.",
            'id_bahasa' => 1,
        ]);


        
        DB::table('overview')->insert([
            'id_overview' => 1,
            'title' => "Sebuah cara baru untuk belajar Bahasa Arab",
            'desc' => "Arabic-Go merupakan platform online khusus belajar Bahasa Arab pertama. Arabic-Go juga menyediakan berbagai fitur unggulan yang siap membantumu!",
            'id_bahasa' => 2,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 2,
            'title' => "Kenapa memilih Arabic-Go ?",
            'desc' => "Selain materi belajar yang sudah terjamin, Arabic-Go juga memiliki mentor profesional yang akan membantumu mempelajari Bahasa Arab dengan cepat.",
            'id_bahasa' => 2,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 3,
            'title' => "Pilih Kelas Unggulan",
            'desc' => "Saatnya memilih sumber belajar dengan bijak. Tidak hanya bahan yang terjamin. Arabic-Go juga memiliki mentor profesional yang akan membantu Anda belajar bahasa Arab dengan cepat.",
            'id_bahasa' => 2,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 4,
            'title' => "Belajar menjadi lebih mudah dengan fitur Beri Harakat",
            'desc' => "Banyak pemula Bahasa Arab yang bingung dalam membaca Arab gundul. Fitur ini hadir untuk memudahkanmu!",
            'id_bahasa' => 2,
        ]);
        
        DB::table('overview')->insert([
            'id_overview' => 5,
            'title' => "Kata mereka tentang Arabic-Go",
            'desc' => "Ulasan yang diposting dijamin 100% asli dan tidak dalam bentuk kerja sama apapun dengan Arabic-Go.",
            'id_bahasa' => 2,
        ]);
    }
}
