<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cats = ["انرژی", "معدن", "صنعت", "کشاورزی", "گردشگری", "حمل و نقل", "عمران"];
        Category::factory(7)->make()->each(function ($category, $index) use ($cats) {
            $category->name = $cats[$index];
            $category->save();
        });
    }
}
