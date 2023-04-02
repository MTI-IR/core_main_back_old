<?php


use App\Models\User;
use Closure;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission, $guard = null)
    {
        $user_id = $request->get('user')->id;
        $user = User::findOrFail($user_id);

        $rolesOrPermissions = is_array($roleOrPermission)
            ? $roleOrPermission
            : explode('|', $roleOrPermission);

        try {
            if ($user->hasPermissionTo('super-admin'))
                return $next($request);
        } catch (Throwable $e) {
            if (!$user->hasAnyRole($rolesOrPermissions) && !$user->hasAnyPermission($rolesOrPermissions)) {
                return  response()->json([
                    "message" => "You have the needed role or permission ",
                    "status" => "403",
                ], 403);
            }
        }
        if (!$user->hasAnyRole($rolesOrPermissions) && !$user->hasAnyPermission($rolesOrPermissions)) {
            return  response()->json([
                "message" => "You have the needed role or permission ",
                "status" => "403",
            ], 403);
        }
        return $next($request);
    }
}
