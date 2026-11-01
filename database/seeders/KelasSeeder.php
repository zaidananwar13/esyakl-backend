<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $level = ["Pemula", "Menengah", "Lanjutan", "Ahli"];
        
        DB::table('kelas')->insert([
            'id_kategori' => 1,
            'id_reviewer' => 1,
            'id_bahasa' => 1,
            'judul' => "Kelas Nahwu Dasar",
            'gambar' => "nahwu-d.png",
            'langkah' => "3 Langkah",
            'level' => "Pemula",
            'key_points' =>  "keypoints 1,keypoints 3,keypoints 2",
            'deskripsi_singkat' => "Kelas Nahwu Dasar",
            'durasi' => "3 Jam",
            'deskripsi_kelas' => "Kelas Nahwu Dasar cocok untuk pemula yang ingin belajar bahasa arab dari awal",
            'tipe_kelas' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('project')->insert([
            'id_kelas' => 1,
            'judul' => "Project Kecil-kecilan",
            'deskripsi' => "Ini project 1",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        
        DB::table('kelas')->insert([
            'id_kategori' => 2,
            'id_reviewer' => 1,
            'id_bahasa' => 1,
            'judul' => "Kelas Shorof Dasar",
            'gambar' => "shorof-d.png",
            'langkah' => "4 Langkah",
            'level' => "Pemula",
            'key_points' =>  "keypoints 1,keypoints 3,keypoints 2",
            'deskripsi_singkat' => "Kelas Shorof Dasar",
            'durasi' => "5 Jam",
            'deskripsi_kelas' => "Kelas Shorof untuk pemula yang ingin belajar bahasa arab dari awal",
            'tipe_kelas' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('project')->insert([
            'id_kelas' => 2,
            'judul' => "Project Kecil-kecilan",
            'deskripsi' => "Ini project 1",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        
        DB::table('kelas')->insert([
            'id_kategori' => 3,
            'id_reviewer' => 1,
            'id_bahasa' => 1,
            'judul' => "Kelas Kaligrafi Dasar",
            'gambar' => "kaligrafi-d.png",
            'langkah' => "3 Langkah",
            'level' => "Pemula",
            'key_points' =>  "keypoints 1,keypoints 3,keypoints 2",
            'deskripsi_singkat' => "Kelas Kaligrafi Dasar",
            'durasi' => "2 Jam",
            'deskripsi_kelas' => "Kelas Kaligrafi  Dasar untuk pemula yang ingin belajar mengenai cara menulis kaligrafi dengan konsep yang mudah untuk dipahami",
            'tipe_kelas' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        DB::table('project')->insert([
            'id_kelas' => 3,
            'judul' => "Project Kecil-kecilan",
            'deskripsi' => "Ini project 1",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
