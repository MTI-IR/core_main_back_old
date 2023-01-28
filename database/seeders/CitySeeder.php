<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::all()->each(function ($state) {
            $cities = City::factory(random_int(2, 5))->make();
            $cities->each(function ($city, $i) use ($state) {
                $city->phone_code = $i . random_int(0, 1000) . $city->phone_code;
                $city->state_id = $state->id;
                $city->save();
            });
        });
    }
}
