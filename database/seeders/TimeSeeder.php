<?php

namespace Database\Seeders;

use App\Models\Time;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 8; $j++) {

                $time = new Time();
                $time->from = Carbon::now()->addHours($i)->addDays($j);
                $time->to = Carbon::now()->addHours($i + 1)->addDays($j);
                $time->save();
            }
        }
    }
}
