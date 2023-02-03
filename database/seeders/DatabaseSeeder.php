<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(StateSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(TagSeeder::class);
        $this->call(RolesAndPermitionSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(ImageSeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(TimeSeeder::class);
        $this->call(TiketSeeder::class);
    }
}
