<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BahasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bahasa')->insert([
            'bahasa' => 'Bahasa Indonesia',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
        DB::table('bahasa')->insert([
            'bahasa' => 'English',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
        DB::table('bahasa')->insert([
            'bahasa' => 'Bahasa Melayu',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
    }
}
