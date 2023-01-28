<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function GuzzleHttp\Promise\each;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::factory(32)->make()->each(function ($state) {
            $state->name = $state->name . fake()->name();
            $state->save();
        });
    }
}
