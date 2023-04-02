<?php


use App\Models\User;
use Closure;
use Spatie\Permission\Models\Permission;
use Throwable;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        $user_id = $request->get('user')->id;
        $user = User::findOrFail($user_id);
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);
        dd("error is here");

        try {
            if ($user->hasPermissionTo('super-admin'))
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

        return  response()->json([
            "message" => "You have the permission : " . $permission,
            "status" => "403",
        ], 403);
    }
}
