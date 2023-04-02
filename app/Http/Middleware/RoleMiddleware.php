<?php


use App\Models\User;
use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role, $guard = null)
    {
        $user_id = $request->get('user')->id;
        $user = User::findOrFail($user_id);

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        try {
            if ($user->hasPermissionTo('super-admin'))
                return $next($request);
        } catch (Throwable $e) {

            if ($user->hasAnyRole($roles)) {
                return  response()->json([
                    "message" => "You have the needed role ",
                    "status" => "403",
                ], 403);
            }
        }
        if ($user->hasAnyRole($roles)) {
            return  response()->json([
                "message" => "You have the needed role ",
                "status" => "403",
            ], 403);
        }
        return $next($request);
    }
}
