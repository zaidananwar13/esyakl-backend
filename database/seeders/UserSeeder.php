<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // for($i = 0; $i < 6; $i++) {
        //     DB::table('user')->insert([
        //         'username' => Str::random(10),
        //         'password' => Str::random(18),
        //         'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        //         'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        //     ]);
        // }
        
        $user = 'nocty';

        DB::table('user')->insert([
            'name' => $user,
            'google_id' => 'default',
            'email' => Str::random(12) . '@mail.com',
            'password' => Hash::make($user),
            'api_token' => hash('sha256', Str::random(60)),
            'created_at' => intval(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => intval(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
    }
}
