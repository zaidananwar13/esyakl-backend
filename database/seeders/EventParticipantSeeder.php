<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach($users as $user) {
            DB::table('event_participant')->insert([
                'id_event' => 1,
                'participant' => $user->id_user,
                'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
                'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
            ]);
        }
    }
}
