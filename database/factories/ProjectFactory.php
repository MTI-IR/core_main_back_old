<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "id" => Str::uuid(),
            "title" => fake()->title(),
            "description" => fake()->text(),
            "our_review" => fake()->text(),
            "show_time" => Carbon::now()->addDays(random_int(-3, 1)),
        ];
    }
}
