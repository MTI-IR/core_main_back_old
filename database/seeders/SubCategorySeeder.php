<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::all()->each(function ($category) {
            $subcategories =  SubCategory::factory(random_int(1, 5))->make();
            foreach ($subcategories as $subcategory) {
                $subcategory->category_id = $category->id;
                $subcategory->save();
            }
        });
    }
}
