<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ReviewerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                
        
        DB::table('class_review')->insert([
            'id_class_student' => 1,
            'star' => 5,
            'content' => 'Mantappu',
            'created_at' => Carbon::now()->format('U'),
            'updated_at' => Carbon::now()->format('U')
        ]);
    }
}
