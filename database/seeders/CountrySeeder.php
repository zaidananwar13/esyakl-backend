<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('country')->insert([
            'country' => 'Indonesia',
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);
        DB::table('country')->insert([
            'country' => 'Malaysia',
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);
        DB::table('country')->insert([
            'country' => 'Singapura',
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);
        DB::table('country')->insert([
            'country' => 'Thailand',
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);
        DB::table('country')->insert([
            'country' => 'Philipina',
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);
    }
}
