<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DiscussionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_discussion')->insert([
            'ownerable' => 'student',
            'id_ownerable' => 11,
            'id_class' => 5,
            'title' => 'Can I ask for a portion of my case fee back if the case is dismissed due to lack of evidence, suspects, and lack of prosecution?',
            'content' => 'Hello i have been charged with sexual assault with choking by an escort. She claimed that i have choked her and she was drunk as well when she made the statement! I have never ever touch her or her neck! She got mad because I didn’t wanna be overcharged by her then tried to rip me off! I denied to overpay her then i left. The police arrived at the scene and saw she had no bruises nothing. No marks nothing at all. Still they charged me with sexual assault with choking. The police even told her that they will call EMS so she can go to the hospital and be evaluated but she denied by “replying “ I’m fine! I don’t know what to do? How can the prosecutors prove beyond a reasonable doubt that I have choke her as she claim?',
            'created_at' => Carbon::now()->format('U'),
            'updated_at' => Carbon::now()->format('U'),
        ]);

        DB::table('class_discussion')->insert([
            'ownerable' => 'student',
            'id_ownerable' => 14,
            'id_class' => 9,
            'title' => 'Can I ask for a portion of my case fee back if the case is dismissed due to lack of evidence, suspects, and lack of prosecution?',
            'content' => 'Hello i have been charged with sexual assault with choking by an escort. She claimed that i have choked her and she was drunk as well when she made the statement! I have never ever touch her or her neck! She got mad because I didn’t wanna be overcharged by her then tried to rip me off! I denied to overpay her then i left. The police arrived at the scene and saw she had no bruises nothing. No marks nothing at all. Still they charged me with sexual assault with choking. The police even told her that they will call EMS so she can go to the hospital and be evaluated but she denied by “replying “ I’m fine! I don’t know what to do? How can the prosecutors prove beyond a reasonable doubt that I have choke her as she claim?',
            'created_at' => Carbon::now()->format('U'),
            'updated_at' => Carbon::now()->format('U'),
        ]);
    }
}
