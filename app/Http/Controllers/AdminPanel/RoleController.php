<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminPanel\BaseResource;
use Illuminate\Http\Request;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'is_admin' => 'boolean'
        ]);
        $is_admin = $request->get('is_admin');
        if ($is_admin) {
            $roles = Role::where('is_admin', true)->get(['id', 'name', 'guard_name']);
        } else {
            $roles = Role::all(['id', 'name', 'guard_name']);
        }
        return new BaseResource(($roles));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles',
            'added_permissions' => 'array',
            'is_admin' => 'boolean'
        ]);
        $is_admin = $request->get('is_admin');
        if ($is_admin) {
            $role = Role::create(['name' => $data["name"], 'guard_name' => 'admin']);
        } else {
            $role = Role::create(['name' => $data["name"], 'guard_name' => 'web']);
        }
        if ($request->get("added_permissions"))
            $role->givePermissionTo($data['added_permissions'], 'admin');
        $roles = Role::all();
        return new BaseResource(($roles));
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(
            [
                'id' => 'required_without:name|integer',
                'name' => 'required_without:id|string',
                'is_admin' => 'boolean'
            ]
        );
        try {
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('id')) {
                    $role = Role::findById($data['id']);
                    if ($role->guard_name != 'admin')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('name'))
                    $role = Role::findByName($data['name'], 'admin');
            } else {
                if ($request->get('id')) {
                    $role = Role::findById($data['id']);
                    if ($role->guard_name != 'web')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('name'))
                    $role = Role::findByName($data['name'], 'web');
            }
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
                "permissions" => "array",
                "is_admin" => "boolean",
            ]
        );
        try {
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('id')) {
                    $role = Role::findById($data['id']);
                    if ($role->guard_name != 'admin')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('name'))
                    $role = Role::findByName($data['name'], 'admin');
            } else {
                if ($request->get('id')) {
                    $role = Role::findById($data['id']);
                    if ($role->guard_name != 'web')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('name'))
                    $role = Role::findByName($data['name'], 'web');
            }
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
            'name' => 'required_without:id|string',
            'is_admin' => 'boolean'
        ]);
        try {
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('id')) {
                    $role = Role::findById($data['id']);
                    if ($role->guard_name != 'admin')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('name'))
                    $role = Role::findByName($data['name'], 'admin');
            } else {
                if ($request->get('id')) {
                    $role = Role::findById($data['id']);
                    if ($role->guard_name != 'web')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('name'))
                    $role = Role::findByName($data['name'], 'web');
            }
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
            $is_admin = $request->get('is_admin');
            if ($is_admin) {
                if ($request->get('role_id')) {
                    $role = Role::findById($data['role_id']);
                    if ($role->guard_name != 'admin')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('role_name'))
                    $role = Role::findByName($data['role_name'], 'admin');
                if ($request->get("removed_permissions"))
                    $role->revokePermissionTo($data['removed_permissions']);
                if ($request->get("added_permissions"))
                    $role->syncPermissions($data['added_permissions']);
                return new BaseResource($role);
            } else {
                if ($request->get('role_id')) {
                    $role = Role::findById($data['role_id']);
                    if ($role->guard_name != 'admin')
                        return response()->json([
                            'massage' => 'Role not found',
                            'status' => '404',
                        ], 404);
                }
                if ($request->get('role_name'))
                    $role = Role::findByName($data['role_name'], 'web');
                if ($request->get("removed_permissions"))
                    $role->revokePermissionTo($data['removed_permissions']);
                if ($request->get("added_permissions"))
                    $role->syncPermissions($data['added_permissions']);
                return new BaseResource($role);
            }
        } catch (Throwable $e) {
            return response()->json([
                'massage' => 'Role not found',
                'status' => '404',
            ], 404);
        }
    }
}
