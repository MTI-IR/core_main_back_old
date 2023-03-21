<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Throwable;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {

            $row_number = 10;
            if ($request->get('row_number')) $row_number = $request->get('row_number');

            $page = 1;
            if ($request->get('page')) $page = $request->get('page');

            $order_by = 'id';
            if ($request->get('order_by')) $order_by = $request->get('order_by');

            $sub_categories = SubCategory::orderBy($order_by)->paginate(
                $row_number,
                [
                    '*'
                ],
                'page',
                $page
            );
            foreach ($sub_categories as $sub_category) {
                $sub_category["category"] = $sub_category->category;
            }
            return new BaseResource($sub_categories);
        } catch (Throwable $e) {
            return response($e, 500);
            return response()->json([
                "message" => "something went wrong.",
                "status" => "500"
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $sub_category = SubCategory::findOrFail($id);
            $sub_category['category'] = $sub_category->category;
            $sub_category['projects'] = $sub_category->projects;
            return new BaseResource($sub_category);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "No sub_category with this id",
                "status" => "404"
            ], 404);
        }
    }
    public function create(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string",
            "category_id" => "required"
        ]);
        try {
            $category = Category::findOrFail($data['category_id']);
            if (!$category)  return response()->json([
                "message" => "category not found!!!.",
                "status" => "404",
            ], 404);
            $sub_category = SubCategory::make();
            $sub_category->name = $data['name'];
            $sub_category->category_id = $data['category_id'];
            $sub_category->save();
            return response()->json([
                "message" => "sub_category created.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response($e, 500);
            return response()->json([
                "message" => "category not found.",
                "status" => "404"
            ], 404);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $sub_categories = $request->get('sub_categories');
            SubCategory::destroy($sub_categories);
            return response()->json([
                "message" => "sub_categories destroyed.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response($e, 500);
            return response()->json([
                "message" => "something went wrong.",
                "status" => "500"
            ], 500);
        }
    }
}
