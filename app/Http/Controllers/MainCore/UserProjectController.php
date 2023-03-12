<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\UserProjectResource;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UserProjectController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            "title"     =>  "required|string",
            "description"  =>  "required|string|",
            "state_name"  =>  "required|string|",
            "state_id"  =>  "required|string|",
            "city_name"  =>  "required|string|",
            "city_id"  =>  "required|string|",
            "category_id"  =>  "required|string|",
            "sub_category_id"  =>  "required|string|",
            "price"  =>  "required|string|",
        ]);
        try {

            $user = $request->get('user');
            $company_id = null;
            $summary = null;
            if ($request->get('company_id')) {
                $company_id = $request->get('company_id');
            }
            if ($request->get('summary')) {
                $summary = $request->get('summary');
            }
            $newId = Str::uuid();

            $newProject = new Project();
            $newProject->id = $newId;
            $newProject->permission_id = 1;
            $newProject->title = $data["title"];
            $newProject->price = $data["price"];
            $newProject->description = $data["description"];
            $newProject->state_name = $data["state_name"];
            $newProject->state_id = $data["state_id"];
            $newProject->city_name = $data["city_name"];
            $newProject->city_id = $data["city_id"];
            $newProject->category_id = $data["category_id"];
            $newProject->sub_category_id = $data["sub_category_id"];
            $newProject->summary = $summary;
            $newProject->company_id = $company_id;
            $newProject->user_id = $user->id;
            $newProject->validated = 1;
            $newProject->show_time = now();

            $imageFiles = $request->file("images");
            $images = [];
            if ($imageFiles) {
                foreach ($imageFiles as $image) {
                    $filename = time() . $newId . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                    $images[] = "http://localhost:8000/images/" . $filename;
                }
            }

            for ($i = 0; $i < count($images); $i++) {
                $image = $newProject->images()->make();
                $image->priority = $i;
                $image->url = $images[$i];
                $image->save();
            }
            $newProject->save();
            return response()->json([
                "massage" => "project created",
                "status" => "200"
            ]);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "server error ",
                "status" => 500,
            ], 404);
        }
    }
    public function edit(Request $request, $id)
    {
        try {
            $user = $request->get('user');
            $project = Project::findOrFail($id);
            if ($user->id != $project->user_id) {
                return response()->json([
                    "massage" => "You don't have the access ",
                    "status" => 403,
                ], 403);
            }
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "There is no project whit this ID ",
                "status" => 404,
            ], 404);
        }

        $data = $request->validate([
            "title"     =>  "required|string",
            "description"  =>  "required|string|",
            "state_name"  =>  "required|string|",
            "state_id"  =>  "required|string|",
            "city_name"  =>  "required|string|",
            "city_id"  =>  "required|string|",
            "category_id"  =>  "required|string|",
            "sub_category_id"  =>  "required|string|",
        ]);

        $company_id = null;
        $summary = null;
        $price = null;
        if ($request->get('company_id')) {
            $company_id = $request->get('company_id');
        }
        if ($request->get('summary')) {
            $summary = $request->get('summary');
        }
        if ($request->get('price')) {
            $price = $request->get('price');
        }
        try {
            $project->title = $data["title"];
            if ($price)
                $project->price = $price;
            $project->description = $data["description"];
            $project->state_name = $data["state_name"];
            $project->state_id = $data["state_id"];
            $project->city_name = $data["city_name"];
            $project->city_id = $data["city_id"];
            $project->category_id = $data["category_id"];
            $project->sub_category_id = $data["sub_category_id"];
            $project->summary = $summary;
            $project->company_id = $company_id;

            $removedImages = $request->get("removed_images");

            if ($removedImages && count($removedImages)) {
                $images = Image::where('url', $removedImages[0]);
                for ($i = 1; $i < count($removedImages); $i++) {
                    $images->orWhere('url', $removedImages[$i]);
                }
                $images = $images->get();
                foreach ($images as $image) {
                    $image->delete();
                    // Storage::delete(public_path('images',$image->url));
                }
            }


            $imageFiles = $request->file("images");
            $images = [];
            if ($imageFiles) {
                foreach ($imageFiles as $image) {
                    $filename = time() . $project->id . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('images'), $filename);
                    $images[] = "http://localhost:8000/images/" . $filename;
                }
            }
            for ($i = 0; $i < count($images); $i++) {
                $image = $project->images()->make();
                $image->priority = 100 + $i;
                $image->url = $images[$i];
                $image->save();
            }
            $images = $project->images()->orderBy('priority');
            $images->each(function ($image, $index) {
                $image->priority = $index;
                $image->save();
            });
            $project->save();
            return response()->json([
                "massage" => "project edited.",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "error",
                "status" => "500",
            ], 500);
        }
    }


    public function show(Request $request, $id)
    {
        $user = $request->get("user");
        try {
            $project = Project::findOrFail($id);
            if ($user->id != $project->user_id) {
                return response()->json([
                    "massage" => "You don't have the access ",
                    "status" => 403,
                ], 403);
            }
            $images = [];
            foreach ($project->images as $image) {
                $images[$image->priority] = $image->url;
            }

            $company = $project->company;
            if ($company) {
                $project->company_name = $company->name;
                $project->company_id = $company->id;
            }
            $edit_able = true;
            $project->edit_able = $edit_able;
            $project->images = $images;
            $project->category_name = $project->category->name;
            $project->category_id =  $project->category->id;
            $project->sub_category_name = $project->sub_category->name;
            $project->sub_category_id =  $project->sub_category->id;
            return new UserProjectResource($project);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "There is no project whit this ID ",
                "status" => 404,
                "error" => $e,
            ], 404);
        }
    }

    // show
    // destroy
}
