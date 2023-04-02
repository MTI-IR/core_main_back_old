<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Throwable;

use Closure;

class MyPermissionsMiddleware
{
    public function handle(Request $request, Closure $next, $permission, $guard = null)
    {
        $user_info = $request->get('user');
        $user_id = $user_info->id;
        $user = User::findOrFail($user_id);
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        try {
            if ($user->hasPermissionTo('super-admin',  'admin'))
                return $next($request);
            foreach ($permissions as $permission) {
                try {
                    Permission::findByName($permission, 'admin');
                    if ($user->hasPermissionTo($permission)) return $next($request);
                } catch (Throwable $e) {
                    Permission::create(["name" => $permission, 'guard_name' => 'admin']);
                    return  response()->json([
                        "message" => "You have the permission :: " . $permission,
                        "status" => "403",
                    ], 403);
                }
            }
        } catch (Throwable $e) {
            try {
                Permission::findByName($permission, 'admin');
                if ($user->hasPermissionTo($permission)) return $next($request);
            } catch (Throwable $e) {
                Permission::create(["name" => $permission, 'guard_name' => 'admin']);
                return  response()->json([
                    "message" => "You have the permission :: " . $permission,
                    "status" => "403",
                ], 403);
            }
        }

        return $user->permissions;
        return  response()->json([
            "message" => "You have the permission : " . $permission,
            "status" => "403",
        ], 403);
    }
}
