<?php

namespace Database\Factories;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            "name" => fake()->name(),
            'phone_number' => fake()->unique()->phoneNumber(),
            "address" => fake()->address(),
            "description" => fake()->text(),
            "validated_at" => now(),
        ];
    }
}
