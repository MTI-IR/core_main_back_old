<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Project;
use App\Models\Tag;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::all()->each(function ($tag) {
            Project::factory(random_int(1, 10))->make()->each(function ($project, $key) use ($tag) {
                $state_id = random_int(1, 32);
                $project->state_id = $state_id;
                $project->city_id = State::find($state_id)->cities()->first()->id;
                $category_id = random_int(1, 3);
                $project->category_id = $category_id;
                $project->sub_category_id = Category::find($category_id)->sub_categories()->first()->id;
                $project->user_id = User::all()->first()->id;
                $project->company_id = Company::all()->first()->id;
                $project->permission_id = random_int(1, 3);
                $project->tag_id = $tag->id;
                $project->save();
            });
        });
    }
}
