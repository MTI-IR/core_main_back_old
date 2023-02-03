<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function ($user) {
            Document::factory(random_int(0, 5))->make()->each(function ($Document, $index) use ($user) {
                $Document->Documentable_id = $user->id;
                $Document->Documentable_type = "App\Models\User";
                $Document->save();
            });
        });
        Company::all()->each(function ($company) {
            Document::factory(random_int(0, 5))->make()->each(function ($Document, $index) use ($company) {
                $Document->Documentable_id = $company->id;
                $Document->Documentable_type = "App\Models\Company";
                $Document->save();
            });
        });
        Project::all()->each(function ($project) {
            Document::factory(random_int(0, 5))->make()->each(function ($document, $index) use ($project) {
                $document->documentable_id = $project->id;
                $document->documentable_type = "App\Models\Company";
                $document->save();
            });
        });
    }
}
