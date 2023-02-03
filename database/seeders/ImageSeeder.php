<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Image;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function ($user) {
            Image::factory(random_int(0, 5))->make()->each(function ($image, $index) use ($user) {
                $image->imageable_id = $user->id;
                $image->imageable_type = "App\Models\User";
                $image->priority = $index;
                $image->save();
            });
        });
        Company::all()->each(function ($company) {
            Image::factory(random_int(0, 5))->make()->each(function ($image, $index) use ($company) {
                $image->imageable_id = $company->id;
                $image->imageable_type = "App\Models\Company";
                $image->priority = $index;
                $image->save();
            });
        });
        Project::all()->each(function ($project) {
            Image::factory(random_int(0, 5))->make()->each(function ($image, $index) use ($project) {
                $image->imageable_id = $project->id;
                $image->imageable_type = "App\Models\Project";
                $image->priority = $index;
                $image->save();
            });
        });
    }
}
