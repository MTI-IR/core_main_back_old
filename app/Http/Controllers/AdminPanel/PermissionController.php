<?php

namespace App\Http\Controllers\AdminPanel;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{


    public function index(Request $request)
    {
        $permissions = Permission::all(['id', 'name', 'priority']);
        foreach ($permissions as $permission) {
            $permission['roles'] = $permission->roles()->get(['id', 'name']);
        }
        return new BaseResource($permissions);
    }

    public function create(Request $request)
    {
        $data = $request->validate(
            [
                "name" => ['required', 'unique:permissions', 'string'],
                "priority" => ['required', 'string'],
            ]
        );
        Permission::create(['name' => $data["name"], 'priority' => $data['priority']]);
        $permissions = Permission::all(['id', 'name', 'priority']);
        return  new BaseResource($permissions);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(
            [
                "name" => ['required_without:id', 'string'],
                "id" => ['required_without:name', 'number'],
            ]
        );
        try {
            if ($request->get('name'))
                $permission = Permission::findByName($data["name"]);
            if ($request->get('id'))
                $permission = Permission::findOrFail($data['id']);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Permission not found",
                "status" => "404"
            ], 404);
        }
        $permission->delete();
        $permissions = Permission::all(['id', 'name', 'priority']);
        return new BaseResource($permissions);
    }

    public function deleteAllPermissions(Request $request)
    {
        $Permissions = Role::truncate();
        return new BaseResource($Permissions);
    }

    public function syncRoles(Request $request)
    {
        $data = $request->validate([
            "roles" => "required|array",
            "name" => ['required_without:id', 'string'],
            "id" => ['required_without:name', 'number'],

        ]);
        if ($request->get('name'))
            $permission = Permission::findByName($data["name"]);
        if ($request->get('id'))
            $permission = Permission::findOrFail($data['id']);
        $roles = $data['roles'];
        $permission->syncRoles($roles);
        return response()->json([
            "message" => "Permission synced with roles",
            "status" => "200"
        ], 200);
    }

    public function removeRole(Request $request)
    {
        $data = $request->validate(
            [
                "permission_name" => ['required_without:permission_id', 'string'],
                "permission_id" => ['required_without:permission_name', 'number'],
                "role_name" => ['required_without:role_id', 'string'],
                "role_id" => ['required_without:role_name', 'number'],
            ]
        );
        if ($request->get('permission_name'))
            $permission = Permission::findByName($data["name"]);
        if ($request->get('permission_id'))
            $permission = Permission::findOrFail($data['id']);
        if ($request->get('role_name'))
            $role = $data["name"];
        if ($request->get('role_id'))
            $role = Role::findOrFail($data['id'])->name;
        $permission->removeRole($role);
        return response()->json([
            "message" => "Role removed",
            "status" => "200"
        ], 200);
    }
}
