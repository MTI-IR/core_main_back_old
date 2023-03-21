<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\SiteInfoResource;
use App\Models\Category;
use App\Models\State;
use Illuminate\Http\Request;
use Throwable;

class SiteInfoController extends Controller
{
    public function states(Request $request)
    {
        $states = State::all()->sortBy('name');
        return new SiteInfoResource($states);
    }



    public function cities(Request $request)
    {
        try {
            $state_id = $request->state_id;
            $state = State::findOrFail($state_id);
            $cities = $state->cities()->orderBy('name', 'desc')->get();
            return new SiteInfoResource($cities);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "There is no state whit this ID",
                "status" => 404,
            ], 404);
        }
    }




    public function categories(Request $request)
    {
        $category = Category::all()->sortBy('id');
        return new SiteInfoResource($category);
    }




    public function subCategories(Request $request)
    {
        try {
            $category_id = $request->category_id;
            $category = Category::findOrFail($category_id);
            $sub_categories = $category->sub_categories()->orderBy('name', 'desc')->get();
            return new SiteInfoResource($sub_categories);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "There is no category whit this ID",
                "status" => 404,
            ], 404);
        }
    }
}
