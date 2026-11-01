<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SilabusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('kategori_silabus')->insert([
            'id_kelas' => 1,
            'judul' => "ND - Pendahuluan",
            'deskripsi' => "Sebelum memulai sesi untuk kelas Nahwu Dasar hendaknya kita menyimak pendahuluan terlebih dahulu.",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('kategori_silabus')->insert([
            'id_kelas' => 2,
            'judul' => "SD - Pendahuluan",
            'deskripsi' => "Sebelum memulai sesi untuk kelas Shorof Dasar  hendaknya kita menyimak pendahuluan terlebih dahulu.",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('kategori_silabus')->insert([
            'id_kelas' => 3,
            'judul' => "KD - Pendahuluan",
            'deskripsi' => "Sebelum memulai sesi untuk kelas Kaligrafi Dasar hendaknya kita menyimak pendahuluan terlebih dahulu.",
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
