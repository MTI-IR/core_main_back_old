<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Models\Company;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File as FacadesFile;
use Throwable;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $row_number = 10;
        if ($request->get('row_number')) $row_number = $request->get('row_number');

        $page = 1;
        if ($request->get('page')) $page = $request->get('page');

        $order_by = 'id';
        if ($request->get('order_by')) $order_by = $request->get('order_by');
        $role = null;
        if ($request->get('role')) $role = $request->get('role');
        if (!$role) {
            $companies = Company::orderBy($order_by)->paginate(
                $row_number,
                [
                    '*'
                ],
                'page',
                $page
            );
        } else {
            $companies = Company::role($role)->orderBy($order_by)->paginate(
                $row_number,
                [
                    '*'
                ],
                'page',
                $page
            );
        }
        foreach ($companies as $company) {
            $company['images'] = $company->images;
        }
        return new BaseResource($companies);
    }

    public function show(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            $company['images'] = $company->images;
            $company['user'] = $company->user;
            $company['documents'] = $company->documents;
            $company['projects'] = $company->projects;
            return new BaseResource($company);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'company not found',
                'status' => '404'
            ], 404);
        }
    }
    public function validateCompanies(Request $request)
    {
        $data = $request->validate([
            "companies" => "array"
        ]);
        $v = true;
        if ($request->get('validation'))
            $v = $request->get('validation');
        try {
            $companies = $request->get('companies');
            foreach ($companies as $company) {
                $c = Company::findOrFail($company);
                $c->validated = $v;
                $c->save();
            }
            return response()->json([
                "message" => "Companies validated",
                'status' => "200"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Something went wrong",
                'status' => "500"
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        $data = $request->validate([
            "id" => "required|string",
            "name" => "required|string",
            "address" => "required|string",
            "phone_number" => "required|digits:11",
            "description" => "required|string",
            "validated" => "boolean",
            "document" => 'file|mimes:zip',
            "images.*"  =>  "image",
        ]);
        try {
            $company = Company::findOrFail($data['id']);
            $company->name = $data["name"];
            $company->address = $data["address"];
            $company->phone_number = $data["phone_number"];
            $company->description = $data["description"];
            if ($request->get('validated'))
                $company->validated = $data["validated"];

            $removed_images = $request->get("removed_images");

            if ($removed_images && count($removed_images)) {
                $images = Image::where('url', $removed_images[0]);
                for ($i = 1; $i < count($removed_images); $i++) {
                    $images->orWhere('url', $removed_images[$i]);
                }
                $images = $images->get();
                foreach ($images as $image) {
                    if ($image->imageable_id = $company->id  && $image->imageable_type == 'App\Models\Company') {
                        $image->delete();
                        if (file_exists(public_path('images') . substr($image->url, 28))) {
                            FacadesFile::delete(public_path('images') . substr($image->url, 28));
                        }
                    }
                }
                $imageFiles = $request->file("images");
                $images = [];
                if ($imageFiles) {
                    $i = 0;
                    foreach ($imageFiles as $image) {
                        $filename = time() . '--' . $i . '--' . $company->id . '.' . $image->getClientOriginalExtension();
                        $i++;
                        $image->move(public_path('images'), $filename);
                        $images[] = "http://localhost:8000/images/" . $filename;
                    }
                }
                for ($i = 0; $i < count($images); $i++) {
                    $image = $company->images()->make();
                    $image->priority = 100 + $i;
                    $image->url = $images[$i];
                    $image->save();
                }
                $images = $company->images()->orderBy('priority');
                $images->each(function ($image, $index) {
                    $image->priority = $index;
                    $image->save();
                });
            }
            $docFile = $request->file("document");
            if ($docFile) {
                $i = 0;
                $filename = time() . '--' . $i . '--' . $company->id . '.' . $docFile->getClientOriginalExtension();
                $i++;
                $docFile->move(storage_path('app/documents'), $filename);
                $doc = $company->documents()->make();
                $doc->url = $filename;
                $doc->save();
            }
            $company->save();
            return response()->json([
                "message" => "Company edited",
                "status" => "200",
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Company not found",
                "status" => "404",
            ], 404);
        }
    }
    public function create(Request $request)
    {
        $data = $request->validate([
            "user_id" => "required|string",
            "name" => "required|string",
            "address" => "required|string",
            "phone_number" => "required|digits:11|unique:companies",
            "description" => "required|string",
            "validated" => "boolean",
            "document" => 'required|file|mimes:zip',
            "images.*"  =>  "image",
        ]);
        try {
            User::findOrFail($data['user_id']);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "User not found",
                "status" => "404",
            ], 404);
        }
        try {
            User::findOrFail($data['user_id']);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "User not found",
                "status" => "404",
            ], 404);
        }
        try {
            User::findOrFail($data['user_id']);
            $company = Company::make();
            $company->id = Str::uuid();
            $company->user_id = $data['user_id'];
            $company->name = $data["name"];
            $company->address = $data["address"];
            $company->phone_number = $data["phone_number"];
            $company->description = $data["description"];
            if ($request->get('validated'))
                $company->validated = $data["validated"];
            $imageFiles = $request->file("images");
            $images = [];
            if ($imageFiles) {
                $i = 0;
                foreach ($imageFiles as $image) {
                    $filename = time() . '--' . $i . '--' . $company->id . '.' . $image->getClientOriginalExtension();
                    $i++;
                    $image->move(public_path('images'), $filename);
                    $images[] = "http://localhost:8000/images/" . $filename;
                }
            }
            for ($i = 0; $i < count($images); $i++) {
                $image = $company->images()->make();
                $image->priority = 100 + $i;
                $image->url = $images[$i];
                $image->save();
            }
            $images = $company->images()->orderBy('priority');
            $images->each(function ($image, $index) {
                $image->priority = $index;
                $image->save();
            });

            $docFile = $request->file("document");
            if ($docFile) {
                $i = 0;
                $filename = time() . '--' . $i . '--' . $company->id . '.' . $docFile->getClientOriginalExtension();
                $i++;
                $docFile->move(storage_path('app/documents'), $filename);
                $doc = $company->documents()->make();
                $doc->url = $filename;
                $doc->save();
            }
            $company->save();
            return response()->json([
                "message" => "Company created",
                "status" => "200",
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Company not found",
                "status" => "404",
            ], 404);
        }
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'companies' => 'array'
        ]);
        Company::destroy($data['companies']);
        return response()->json([
            "message" => 'Companies removed',
            'status' => '200'
        ], 200);
    }

    public function projects(Request $request)
    {
        $data = $request->validate([
            'id' => 'string'
        ]);
        try {
            $company = Company::findOrFail($data['id']);
        } catch (Throwable $e) {
            return response()->json([
                "message" => 'There is no company with this id',
                "status" => "404"
            ], 404);
        }
        return new BaseResource($company->projects);
    }

    public function document(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);
            $document = $company->documents()->orderBy('created_at', 'desc')->first();
            if ($document) {
                $document = $document->url;
                $doc_path = storage_path('app/documents/' . $document);
                if (file_exists($doc_path)) { // Checking if file exist
                    $headers = [
                        'Content-Type' => 'application/zip'
                    ];
                    return response()->download($doc_path, 'Test File', $headers, 'inline');
                }
            }
            return response()->json([
                "message" => "No document for this company",
                "status" => "404"
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "company not found.",
                "status" => "404"
            ], 404);
        }
    }
}
