<?php

namespace App\Http\Controllers\AdminPanel;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;

use function PHPSTORM_META\map;

class PermissionController extends Controller
{


    public function index(Request $request)
    {
        $is_admin = $request->get('is_admin');
        if ($is_admin) {
            $permissions = Permission::where("guard_name", 'admin')->get(['id', 'name', 'priority', 'guard_name']);
            foreach ($permissions as $permission) {
                $permission['roles'] = $permission->roles()->get(['id', 'name', 'guard_name']);
            }
        } else {
            $permissions = Permission::where("guard_name", 'web')->get(['id', 'name', 'priority', 'guard_name']);
            foreach ($permissions as $permission) {
                $permission['roles'] = $permission->roles()->get(['id', 'name', 'guard_name']);
            }
        }
        return new BaseResource($permissions);
    }

    public function create(Request $request)
    {
        $data = $request->validate(
            [
                "name" => ['required', 'unique:permissions', 'string'],
                "priority" => ['required_without:is_admin', 'string'],
                "is_admin" => ['boolean'],
            ]
        );
        $is_admin = $request->get('is_admin');
        if ($is_admin) {
            Permission::create(['name' => $data["name"], 'priority' => 0, 'guard_name' => 'admin']);
            $permissions = Permission::where('guard_name', 'admin')->get(['id', 'name', 'priority', 'guard_name']);
        } else {
            Permission::create(['name' => $data["name"], 'priority' => $data['priority'], 'guard_name' => 'web']);
            $permissions = Permission::where('guard_name', 'web')->get(['id', 'name', 'priority', 'guard_name']);
        }
        return  new BaseResource($permissions);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(
            [
                "name" => ['required_without:id', 'string'],
                "id" => ['required_without:name', 'number'],
                "is_admin" => ['boolean'],
            ]
        );
        try {
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('name'))
                    $permission = Permission::findByName($data["name"], 'admin');
                if ($request->get('id')) {
                    $permission = Permission::findOrFail($data['id']);
                    if ($permission->guard_name != 'admin')
                        return response()->json([
                            'message' => 'Permission guard is not admin',
                            'status' => '403'
                        ], 403);
                }
            } else {
                if ($request->get('name'))
                    $permission = Permission::findByName($data["name"], 'web');
                if ($request->get('id')) {
                    $permission = Permission::findOrFail($data['id']);
                    if ($permission->guard_name != 'web')
                        return response()->json([
                            'message' => 'Permission guard is not web',
                            'status' => '403'
                        ], 403);
                }
            }
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
        $Permissions = Permission::truncate();
        return new BaseResource($Permissions);
    }

    public function syncRoles(Request $request)
    {
        $data = $request->validate([
            "roles" => "required|array",
            "name" => ['required_without:id', 'string'],
            "id" => ['required_without:name', 'number'],
            "is_admin" => ['boolean'],

        ]);
        $is_admin = $request->get('is_admin');
        if ($is_admin) {
        }
        try {
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('name'))
                    $permission = Permission::findByName($data["name"], 'admin');
                if ($request->get('id')) {
                    $permission = Permission::findOrFail($data['id']);
                    if ($permission->guard_name != 'admin')
                        return response()->json([
                            'message' => 'Permission guard is not admin',
                            'status' => '403'
                        ], 403);
                }
            } else {
                if ($request->get('name'))
                    $permission = Permission::findByName($data["name"], 'web');
                if ($request->get('id')) {
                    $permission = Permission::findOrFail($data['id']);
                    if ($permission->guard_name != 'web')
                        return response()->json([
                            'message' => 'Permission guard is not web',
                            'status' => '403'
                        ], 403);
                }
            }
            $roles = $data['roles'];
            $permission->syncRoles($roles);
            return response()->json([
                "message" => "Permission synced with roles",
                "status" => "200"
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Permission or role not found on the guard",
                "status" => "404"
            ], 404);
        }
    }

    public function removeRole(Request $request)
    {
        $data = $request->validate(
            [
                "permission_name" => ['required_without:permission_id', 'string'],
                "permission_id" => ['required_without:permission_name', 'number'],
                "role_name" => ['required_without:role_id', 'string'],
                "role_id" => ['required_without:role_name', 'number'],
                "is_admin" => ['boolean'],
            ]
        );
        try {
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('name'))
                    $permission = Permission::findByName($data["name"], 'admin');
                if ($request->get('id')) {
                    $permission = Permission::findOrFail($data['id']);
                    if ($permission->guard_name != 'admin')
                        return response()->json([
                            'message' => 'Permission guard is not admin',
                            'status' => '403'
                        ], 403);
                }
            } else {
                if ($request->get('name'))
                    $permission = Permission::findByName($data["name"], 'web');
                if ($request->get('id')) {
                    $permission = Permission::findOrFail($data['id']);
                    if ($permission->guard_name != 'web')
                        return response()->json([
                            'message' => 'Permission guard is not web',
                            'status' => '403'
                        ], 403);
                }
            }
        } catch (Throwable $e) {
            return response()->json([
                "message" => "Permission not found",
                "status" => "404"
            ], 404);
        }
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
