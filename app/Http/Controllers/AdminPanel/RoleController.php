<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::all(['id', 'name']);
        return new BaseResource(($roles));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles',
            'added_permissions' => 'array',
        ]);
        $role = Role::create(['name' => $data["name"]]);
        $role->givePermissionTo($data['added_permissions']);
        $roles = Role::all();
        return new BaseResource(($roles));
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(
            [
                'id' => 'required_without:name|integer',
                'name' => 'required_without:id|string'
            ]
        );
        try {
            if ($request->get('id'))
                $role = Role::findById($data['id']);
            if ($request->get('name'))
                $role = Role::findByName($data['name']);
        } catch (Throwable $role) {
            return response("role not found ", 404);
        }
        $role->delete();
        $roles = Role::all();
        return new BaseResource(($roles));
    }

    public function deleteAllRole(Request $request)
    {
        $roles = Role::truncate();
        return new BaseResource(($roles));
    }

    //--------------------------------------------------------------------------
    //give or remove permissions



    public function syncPermissions(Request $request)
    {
        $data = $request->validate(
            [
                'id' => 'required_without:name|integer',
                'name' => 'required_without:id|string',
                "permissions" => "array"
            ]
        );
        try {
            if ($request->get('id'))
                $role = Role::findById($data['id']);
            if ($request->get('name'))
                $role = Role::findByName($data['name']);
        } catch (Throwable $role) {
            return response()->json([
                'massage' => 'Role not found',
                'status' => '404',
            ], 404);
        }
        $role->syncPermissions($data['permissions']);
        return new BaseResource($role->permissions);
    }

    public function show(Request $request)
    {
        $data = $request->validate([
            'id' => 'required_without:name|integer',
            'name' => 'required_without:id|string'
        ]);
        try {
            if ($request->get('id'))
                $role = Role::findById($data['id']);
            if ($request->get('name'))
                $role = Role::findByName($data['name']);
            $rolePermissions = $role->permissions()->get(['id', 'name', 'priority']);
            $role['permissions'] = $rolePermissions;
            return new BaseResource($role);
        } catch (Throwable $e) {
            return response()->json([
                'massage' => 'Role not found',
                'status' => '404',
            ], 404);
        }
    }

    public function changeRole(Request $request)
    {
        $data = $request->validate([
            'role_id' => 'required_without:role_name|integer',
            'role_name' => 'required_without:role_id|string',
            'added_permissions' => 'array',
            'removed_permissions' => 'array'
        ]);
        try {
            if ($request->get('role_id'))
                $role = Role::findById($data['role_id']);
            if ($request->get('role_name'))
                $role = Role::findByName($data['role_name']);
            if ($request->get("removed_permissions"))
                $role->revokePermissionTo($data['removed_permissions']);
            if ($request->get("added_permissions"))
                $role->syncPermissions($data['added_permissions']);
            return new BaseResource($role);
        } catch (Throwable $e) {
            return $e;
            return response()->json([
                'massage' => 'Role not found',
                'status' => '404',
            ], 404);
        }
    }
}
