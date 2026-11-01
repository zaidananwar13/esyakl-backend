<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SilabusCheckerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::select('id_user')->get()->toArray();

        foreach($users as $user) {
            DB::table('silabus_checker')->insert([
                'id_user' => $user['id_user'],
                'id_kategori_silabus' => 1,
                'id_sub_kategori_silabus' => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
