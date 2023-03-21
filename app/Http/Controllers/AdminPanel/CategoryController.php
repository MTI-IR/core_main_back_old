<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends Controller
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

            $categories = Category::orderBy($order_by)->paginate(
                $row_number,
                [
                    '*'
                ],
                'page',
                $page
            );
            return new BaseResource($categories);
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
            $category = Category::findOrFail($id);
            $category['sub_categories'] = $category->sub_categories;
            $category['projects'] = $category->projects;
            return new BaseResource($category);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "No category with this id",
                "status" => "404"
            ], 404);
        }
    }
    public function create(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string"
        ]);
        try {
            $category = Category::make();
            $category->name = $data['name'];
            $category->save();
            return response()->json([
                "message" => "category created.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "This category is already exist",
                "status" => "405"
            ], 405);
            return response($e, 500);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $categories = $request->get('categories');
            Category::destroy($categories);
            return response()->json([
                "message" => "categories destroyed.",
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
