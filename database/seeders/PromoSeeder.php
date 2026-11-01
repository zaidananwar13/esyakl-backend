<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('promo')->insert([
            'percentage' => true,
            'value' => '50',
            'expired_at' => intval(Carbon::now()->addMonth(1)->format('U')),
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);

        DB::table('promo')->insert([
            'code' => 'xoxoxo',
            'percentage' => false,
            'value' => '15000',
            'expired_at' => intval(Carbon::now()->addMonth(1)->format('U')),
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);
    }
}
