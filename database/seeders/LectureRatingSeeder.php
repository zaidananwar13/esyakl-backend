<?php

namespace Database\Seeders;

use App\Models\ClassLecture;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LectureRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lecture = ClassLecture::first();
        $users = User::all();

        foreach($users as $user) {
            DB::table('lecture_rating')->insert([
                'id_lecture' => $lecture->id_class_lecture,
                'id_user' => $user->id_user,
                'rating' => rand(3, 5),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
        
    }
}
