<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            "name"     =>  "required|string",
            "phone_number"  =>  "required|string|",
            "address"  =>  "required|string|",
            "description"  =>  "required|string|",
        ]);
        $user = $request->get('user');
        $newCompany = Company::make();
        $newId = Str::uuid();
        $newCompany->id = $newId;
        $newCompany->name = $data["name"];
        $newCompany->address = $data["address"];
        $newCompany->description = $data["description"];
        $newCompany->phone_number = $data["phone_number"];
        $newCompany->user_id = $user->id;

        $documentFile = $request->file("document");
        $filename = time() . $newId . '.' . $documentFile->getClientOriginalExtension();
        $documentFile->move(storage_path() . 'privateFiles/documents', $filename);
        $documentFiles[] = $filename;

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
            $image = $newCompany->images()->make();
            $image->priority = $i;
            $image->url = $images[$i];
            $image->save();
        }
        $newCompany->save();
    }
}
