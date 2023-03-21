<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use App\Http\Resources\AdminPanel\UsersResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Throwable;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $row_number = 10;
        if ($request->get('row_number')) $row_number = $request->get('row_number');

        $page = 1;
        if ($request->get('page')) $page = $request->get('page');

        $order_by = 'first_name';
        if ($request->get('order_by')) $order_by = $request->get('order_by');
        $role = null;
        if ($request->get('role')) $role = $request->get('role');
        if (!$role) {
            $users = User::with('roles')->orderBy($order_by)->paginate(
                $row_number,
                [
                    'id', 'first_name', 'last_name', 'phone_number',
                    'national_code', 'email_verified_at',
                    'phone_number_verified_at', 'validated'
                ],
                'page',
                $page
            );
        } else {
            $users = User::role($role)->orderBy($order_by)->paginate(
                $row_number,
                [
                    'id', 'first_name', 'last_name', 'phone_number',
                    'national_code', 'email_verified_at',
                    'phone_number_verified_at', 'validated'
                ],
                'page',
                $page
            );
        }
        foreach ($users as $user) {
            $user['images'] = $user->images;
        }
        return new UsersResource($users);
    }
    public function show(Request $request, $id)
    {
        try {

            $user = User::findOrFail($id);
            $user['images'] = $user->images;
            $user['companies'] = $user->companies;
            $user['roles'] = $user->roles;
            $user['documents'] = $user->documents;
            $user['projects'] = $user->projects;
            $user['tickets'] = $user->tickets;
            $user['marks'] = $user->marks;
            $user['permissions'] = $user->getPermissionsViaRoles();
            return new BaseResource($user);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'user not found',
                'status' => '404'
            ], 404);
        }
    }
    public function edit(Request $request)
    {
        $data = $request->validate([
            "id" => "required|string",
            "first_name" => "string",
            "last_name" => "string",
            "national_code" => "digits:10",
            "email" => "email",
            "phone_number" => 'digits:11',
            "validated" => "boolean",
            "password" => "string",
            "role" => "string",
        ]);
        try {
            $user = User::findOrFail($data['id']);
            if ($request->get('first_name')) {
                $user->first_name = $data['first_name'];
            }
            if ($request->get('last_name')) {
                $user->last_name = $data['last_name'];
            }
            if ($request->get('national_code')) {
                $user->national_code = $data['national_code'];
            }
            if ($request->get('email')) {
                $user->email = $data['email'];
            }
            if ($request->get('phone_number')) {
                $user->phone_number = $data['phone_number'];
            }
            if ($request->get('validated')) {
                $user->validated = $data['validated'];
            }
            if ($request->get('password')) {
                $data["password"] = Hash::make($data["password"]);
                $user->password = $data['password'];
            }
            $newImage = $request->file("image");
            if ($newImage) {
                $images = Image::where('imageable_id', $user->id)->where('imageable_type', 'App\Models\User');
                $images = $images->get();
                foreach ($images as $image) {
                    $image->delete();
                    if (file_exists(public_path('images') . substr($image->url, 28))) {
                        File::delete(public_path('images') . substr($image->url, 28));
                    }
                }
                $filename = time() . $user->id . '.' . $newImage->getClientOriginalExtension();
                $newImage->move(public_path('images'), $filename);
                $image_url = "http://localhost:8000/images/" . $filename;
                $image = $user->images()->make();
                $image->priority = 0;
                $image->url = $image_url;
                $image->save();
            }
            try {
                if ($request->get('role')) {

                    $role = "";
                    if (count($user->roles)) $role = $user->roles[0]['name'];
                    if ($role != $data['role']) {
                        for ($i = 0; $i < count($user->roles); $i++) {
                            $user->removeRole($user->roles[$i]['name']);
                        }
                        $user->assignRole($data['role']);
                        $DirectPermissions = $user->getDirectPermissions();
                        $role = Role::findByName($data['role']);
                        $rolePemissions = $role->permissions->pluck('name');
                        foreach ($DirectPermissions as $p) {
                            foreach ($rolePemissions as $r) {
                                if ($r == $p['name']) $user->revokePermissionTo($r);
                            }
                        }
                    }
                }
            } catch (Throwable $e) {
                return response()->json([
                    "message" => "Role not found",
                    "status" => "404"
                ], 404);
            }
            $user->save();
            return response()->json([
                "message" => "User changed",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                "message" => "User not found",
                "status" => "404"
            ], 404);
        }
    }
    public function create(Request $request)
    {
        $data = $request->validate([
            "first_name" => "required_with:last_name|string",
            "last_name" => "required_with:first_name|string",
            "national_code" => "digits:10",
            "email" => "email",
            "phone_number" => 'required|digits:11|unique:users',
            "validated" => "boolean",
            "password" => "string",
            "is_admin" => "boolean",
        ]);

        try {
            $user = User::make();
            $user->id = Str::uuid();
            if ($request->get('first_name'))
                $user->first_name = $data['first_name'];
            if ($request->get('last_name'))
                $user->last_name = $data['last_name'];
            if ($request->get('national_code')) {
                $user->national_code = $data['national_code'];
            }
            if ($request->get('email')) {
                $user->email = $data['email'];
            }
            $user->phone_number = $data['phone_number'];
            if ($request->get('validated')) {
                $user->validated = $data['validated'];
            }
            if ($request->get('password')) {
                $data["password"] = Hash::make($data["password"]);
                $user->password = $data['password'];
            }
            if ($request->get('is_admin')) {
                $user->is_admin = $data['is_admin'];
            }
            $newImage = $request->file("image");
            if ($newImage) {
                $filename = time() . $user->id . '.' . $newImage->getClientOriginalExtension();
                $newImage->move(public_path('images'), $filename);
                $image_url = "http://localhost:8000/images/" . $filename;
                $image = $user->images()->make();
                $image->priority = 0;
                $image->url = $image_url;
                $image->save();
            }
            $user->save();
            return response()->json([
                "message" => "User created",
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
        try {
            $id = $request->get('id');
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                "message" => "User deleted",
                'status' => "200"
            ], 200);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                "message" => "Something went wrong",
                'status' => "500"
            ], 500);
        }
    }
    public function destroyUsers(Request $request)
    {
        try {
            $users = $request->get('users');
            User::destroy($users);
            return response()->json([
                "message" => "Users deleted",
                'status' => "200"
            ], 200);
            return response()->json([
                "message" => "User deleted",
                'status' => "200"
            ], 200);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                "message" => "Something went wrong",
                'status' => "500"
            ], 500);
        }
    }
    public function validateUsers(Request $request)
    {
        $data = $request->validate([
            "users" => "array"
        ]);
        $v = true;
        if ($request->get('validation'))
            $v = $request->get('validation');
        try {
            $users = $request->get('users');
            foreach ($users as $user) {
                $u = User::findOrFail($user);
                $u->validated = $v;
                $u->save();
            }
            return response()->json([
                "message" => "Users validated",
                'status' => "200"
            ], 200);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                "message" => "Something went wrong",
                'status' => "500"
            ], 500);
        }
    }

    public function assignRole(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|string',
            'role_name' => 'required_without:role_id|string',
            'role_id' => 'required_without:role_name',
        ]);
        try {
            $user = User::findOrFail($data['user_id']);
        } catch (Throwable $e) {
            return response()->json([
                'massage' => 'User not found',
                'status' => '404',
            ], 404);
        }
        try {
            if ($request->get('role_id'))
                $role = Role::findById($data['role_id'])->name;
            if ($request->get('role_name'))
                $role = $data['role_name'];
            $user->assignRole($role);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                'massage' => 'Role not found',
                'status' => '404',
            ], 404);
        }
        return response()->json([
            'massage' => 'Role assigned',
            'status' => '200',
        ], 200);
    }
    public function addPermission(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|string',
            'permissions_name' => 'required_without:permissions_id|array',
            'permissions_id' => 'required_without:permissions_name|array',
        ]);
        try {
            $user = User::findOrFail($data['user_id']);
        } catch (Throwable $e) {
            return response()->json([
                'massage' => 'User not found',
                'status' => '404',
            ], 404);
        }
        try {
            if ($request->get('permissions_name'))
                $permissions = $data['permissions_name'];
            if ($request->get('permissions_id'))
                $permissions = $data['permissions_id'];
            $user->givePermissionTo($permissions);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                'massage' => 'Permissions not found',
                'status' => '404',
            ], 404);
        }
        return response()->json([
            'massage' => 'Permission assigned',
            'status' => '200',
        ], 200);
    }


    public function removePermission(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|string',
            'permission_name' => 'required_without:permission_id|string',
            'permission_id' => 'required_without:permission_name',
        ]);
        try {
            $user = User::findOrFail($data["user_id"]);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                'massage' => 'User not found',
                'status' => '404',
            ], 404);
        }
        try {
            if ($request->get('permission_name'))
                $permission = $data['permission_name'];
            if ($request->get('permission_id'))
                $permission = $data['permission_id'];
            $user->revokePermissionTo($permission);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                'massage' => 'Permission not found',
                'status' => '404',
            ], 404);
        }
        return response()->json([
            'massage' => 'Permission removed',
            'status' => '200',
        ], 200);
    }

    public function changePermissions(Request $request)
    {

        $data = $request->validate([
            'user_id' => 'required|string',
            "added_permissions" => "array",
            "removed_permissions" => "array",
        ]);
        try {
            $user = User::findOrFail($data["user_id"]);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                'massage' => 'User not found',
                'status' => '404',
            ], 404);
        }
        if ($request->get("removed_permissions"))
            foreach ($data["removed_permissions"] as $p) {
                $user->revokePermissionTo($p);
            }
        if ($request->get("added_permissions"))
            $user->givePermissionTo($data['added_permissions']);
        return response()->json([
            'massage' => 'Permissions changed',
            'status' => '200',
        ], 200);
    }
}
