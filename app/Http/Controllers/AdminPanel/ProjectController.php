<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Http\Resources\AdminPanel\ProjectsResource;
use App\Models\Category;
use App\Models\City;
use App\Models\Company;
use App\Models\Image;
use App\Models\Project;
use App\Models\State;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Throwable;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $row_number = 10;
        if ($request->get('row_number')) $row_number = $request->get('row_number');

        $page = 1;
        if ($request->get('page')) $page = $request->get('page');

        $order_by = 'show_time';
        if ($request->get('order_by')) $order_by = $request->get('order_by');

        $projects = Project::orderBy($order_by)->paginate(
            $row_number,
            [
                '*'
            ],
            'page',
            $page
        );

        foreach ($projects as $project) {
            $project['images'] = $project->images;
            $project['category'] = $project->category;
            $project['sub_category'] = $project->sub_category;
            $project['state'] = $project->state;
            $project['city'] = $project->city;
            $project['user'] = $project->user;
            $project['tag'] = $project->tag;
        }
        return new ProjectsResource($projects);
    }

    public function edit(Request $request)
    {
        $data = $request->validate([
            'id' => "string",
            "title"     =>  "required|string",
            "description"  =>  "required|string|",
            "state_id"  =>  "required|string|",
            "city_id"  =>  "required|string|",
            "category_id"  =>  "required|string|",
            "sub_category_id"  =>  "required|string|",
            "permission_id" => "numeric",
            "our_review" => "string",
            "validated" => "boolean",
            "removed_images.*" => "string",
            "document" => 'file|mimes:zip',
            "images.*" => "image"
        ]);
        $project = Project::findOrFail($data['id']);
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

            if ($request->get('our_review')) {
                $project->our_review = $data['our_review'];
            }

            if ($request->get('validated')) {
                $project->validated = $data['validated'];
            }

            if ($request->get('permission_id')) {
                $project->permission_id = $data['permission_id'];
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
                    if ($image->imageable_id == $project->id && $image->imageable_type == 'App\Models\Project') {
                        $image->delete();
                        if (file_exists(public_path('images') . substr($image->url, 28))) {
                            File::delete(public_path('images') . substr($image->url, 28));
                        }
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

            $docFile = $request->file("document");
            if ($docFile) {
                $i = 0;
                $filename = time() . '--' . $i . '--' . $project->id . '.' . $docFile->getClientOriginalExtension();
                $i++;
                $docFile->move(storage_path('app/documents'), $filename);
                $doc = $project->documents()->make();
                $doc->url = $filename;
                $doc->save();
            }
            $project->save();
            return response()->json([
                "message" => "project edited.",
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

    public function create(Request $request)
    {

        $data = $request->validate([
            'user_id' => "required|string",
            "title"     =>  "required|string",
            "description"  =>  "required|string",
            "state_name"  =>  "string",
            "state_id"  =>  "required|string",
            "city_name"  =>  "string",
            "city_id"  =>  "required|string",
            "category_id"  =>  "required|string",
            "sub_category_id"  =>  "required|string",
            "company_id"  =>  "string",
            "permission_id"  =>  "string",
            "our_review"  =>  "string",
            "validated"  =>  "boolean",
            "document" => 'required|file|mimes:zip',
            "images.*"  =>  "image",
        ]);
        $project = Project::make();
        $project->id = Str::uuid();
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
            $project->user_id = $data['user_id'];

            $state = State::findOrFail($data["state_id"]);
            $project->state()->associate($state);
            $project->state_name = $state->name;

            $city = City::findOrFail($data["city_id"]);
            $project->city()->associate($city);
            $project->city_name = $city->name;

            $category = Category::findOrFail($data["category_id"]);
            $project->category()->associate($category);

            $sub_category = SubCategory::findOrFail($data["sub_category_id"]);
            $project->sub_category()->associate($sub_category);

            $permission_id = Permission::all()->sortBy('priority')->first()->id;
            if ($request->get('permission_id')) {
                $permission_id = $data['permission_id'];
            }
            $project->permission_id = $permission_id;

            if ($request->get('our_review')) {
                $project->our_review = $data['our_review'];
            }
            if ($request->get('validated')) {
                $project->validated = $data['validated'];
            }

            if ($request->get('company_id') != null) {
                $company = Company::findOrFail($request->get('company_id'));
                if ($company) {
                    $project->company()->associate($company);
                }
            }
            $project->summary = $summary;


            $project->save();
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

            $docFile = $request->file("document");
            if ($docFile) {
                $i = 0;
                $filename = time() . '--' . $i . '--' . $project->id . '.' . $docFile->getClientOriginalExtension();
                $i++;
                $docFile->move(storage_path('app/documents'), $filename);
                $doc = $project->documents()->make();
                $doc->url = $filename;
                $doc->save();
            }


            return response()->json([
                "message" => "Project created.",
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

    public function show(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project['images'] = $project->images;
            $project['category'] = $project->category;
            $project['sub_category'] = $project->sub_category;
            $project['state'] = $project->state;
            $project['city'] = $project->city;
            $project['user'] = $project->user;
            $project['tag'] = $project->tag;
            $project['company'] = $project->company;
            return  new BaseResource($project);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "something went wrong.",
                "status" => "500"
            ], 500);
        }
    }

    public function document(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $document = $project->documents()->orderBy('created_at', 'desc')->first()->url;
            $doc_path = storage_path('app/documents/' . $document);
            if (file_exists($doc_path)) { // Checking if file exist
                $headers = [
                    'Content-Type' => 'application/zip'
                ];
                return response()->download($doc_path, 'Test File', $headers, 'inline');
            }
            return response()->json([
                "message" => "No document for this project",
                "status" => "404"
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Project not found.",
                "status" => "404"
            ], 404);
        }
    }

    public function validateProjects(Request $request)
    {
        $data = $request->validate([
            "projects" => "array"
        ]);
        $v = true;
        if ($request->get('validation'))
            $v = $request->get('validation');
        try {
            $projects = $request->get('projects');
            foreach ($projects as $project) {
                $c = Project::findOrFail($project);
                $c->validated = $v;
                $c->save();
            }
            return response()->json([
                "message" => "projects validated",
                'status' => "200"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Something went wrong",
                'status' => "500"
            ], 500);
        }
    }
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'projects' => 'array'
        ]);
        Project::destroy($data['projects']);
        return response()->json([
            "message" => 'projects removed',
            'status' => '200'
        ], 200);
    }
}
