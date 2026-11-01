<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project')->insert([
            'id_kelas' => 9,
            'title' => "Project Nahwu - Intermediate",
            'description' => "<p>Currently, Backend Development is becoming one of the most important stacks in building apps. Whether it&#39;s web apps, mobile apps, or desktop apps, all require a backend as a liaison between platforms.</p><p>In this workshop, we will learn firsthand about the basic Rest API, what it is like and how to make it for beginners.</p>",
            'terms' => "<ul>
                <li>Harun ar-Rashid (763&ndash;809), the fifth caliph of the Abbasid dynasty founded the first hospital in the 9th century.</li>
                <li>Ibnu Nafis (1213-1288), was a Syrian scientist who discovered pulmonary circulation and whose writings on the subject were used in the West until the 16th century.</li>
                <li>Nasir ad-Din al-Tusi (1201-1274), was an Iranian scientist who developed the most advanced diagram of the solar system of his time. It was his work that inspired Nicolaus Copernicus (1473-1543) 250 years later.</li>
            </ul>",
            'appraisal'=> "You must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of the system.;Expound the actual teachings of the great explorer of the truth, the master-builder.;No one rejects, dislikes, or avoids pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.",
            // 'is_submission' => false,
            'pass_point' => 80,
            'created_at' => intval(Carbon::now()->format('U')),
            'updated_at' => intval(Carbon::now()->format('U'))
        ]);

    }
}
