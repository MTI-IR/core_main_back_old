<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tiket;
use App\Models\Time;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Time::all()->each(function ($time) {
            Tiket::factory(random_int(0, 1))->create([
                "user_id" => User::all()->random(),
                "project_id" => Project::all()->random(),
                "time_id" => $time->id,
            ]);
        });
    }
}
