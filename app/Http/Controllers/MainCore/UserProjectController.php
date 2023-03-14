<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\ProjectsResource;
use App\Http\Resources\MainCore\UserProjectResource;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Image;
use App\Models\Project;
use App\Models\State;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
            $newProject->description = $data["description"];
            $newProject->user_id = $user->id;
            $newProject->validated = 1;
            $newProject->show_time = now();

            $newProject->description = $data["description"];
            $state = State::findOrFail($data["state_id"]);
            $newProject->state()->dissociate();
            $newProject->state()->associate($state);
            $newProject->state_name = $state->name;

            $city = City::findOrFail($data["city_id"]);
            $newProject->city()->dissociate();
            $newProject->city()->associate($city);
            $newProject->city_name = $city->name;

            $category = Category::findOrFail($data["category_id"]);
            $newProject->category()->dissociate();
            $newProject->category()->associate($category);

            $sub_category = SubCategory::findOrFail($data["sub_category_id"]);
            $newProject->sub_category()->dissociate();
            $newProject->sub_category()->associate($sub_category);

            if ($request->get('company_id') != null) {
                echo ("company_id is : ");
                echo ($request->get('company_id'));
                $company = Company::findOrFail($request->get('company_id'));
                if ($company) {
                    $newProject->company()->dissociate();
                    $newProject->company()->associate($company);
                }
            }
            $newProject->summary = $summary;



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
        $price = 0;
        if ($request->get('summary')) {
            $summary = $request->get('summary');
        }
        if ($request->get('price')) {
            $price = $request->get('price');
        }
        try {
            $project->title = $data["title"];
            if ($price != null)
                $project->price = $price;
            $project->description = $data["description"];
            $state = State::findOrFail($data["state_id"]);
            $project->state()->dissociate();
            $project->state()->associate($state);
            $project->state_name = $state->name;

            $city = City::findOrFail($data["city_id"]);
            $project->city()->dissociate();
            $project->city()->associate($city);
            $project->city_name = $city->name;

            $category = Category::findOrFail($data["category_id"]);
            $project->category()->dissociate();
            $project->category()->associate($category);

            $sub_category = SubCategory::findOrFail($data["sub_category_id"]);
            $project->sub_category()->dissociate();
            $project->sub_category()->associate($sub_category);

            if ($request->get('company_id') != null) {
                $company = Company::findOrFail($request->get('company_id'));
                if ($company) {
                    $project->company()->dissociate();
                    $project->company()->associate($company);
                }
            }
            $project->summary = $summary;

            $removedImages = $request->get("removed_images");

            if ($removedImages && count($removedImages)) {
                $images = Image::where('url', $removedImages[0]);
                for ($i = 1; $i < count($removedImages); $i++) {
                    $images->orWhere('url', $removedImages[$i]);
                }
                $images = $images->get();
                foreach ($images as $image) {
                    $image->delete();
                    if (file_exists(public_path('images') . substr($image->url, 28))) {
                        File::delete(public_path('images') . substr($image->url, 28));
                    }
                }
            }

            $imageFiles = $request->file("images");
            $images = [];
            if ($imageFiles) {
                $i = 0;
                foreach ($imageFiles as $image) {
                    $filename = time() . '--' . $i . '--' . $project->id . '.' . $image->getClientOriginalExtension();
                    $i++;
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
            return response($e, 500);
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

    public function userProjects(Request $request)
    {
        $user = $request->get('user');
        $projects = User::findOrFail($user->id)->projects()->paginate(18, ['*'], 'page', 1);
        return new ProjectsResource($projects);
    }
    // destroy
}
