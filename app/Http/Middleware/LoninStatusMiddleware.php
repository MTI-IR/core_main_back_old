<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Throwable;

class LoninStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = ($request->header()["authorization"][0]);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "Token is not found",
                "status" => "404",
            ], 404);
        }
        if (!$token) return  response()->json([
            "massage" => "Token is not found",
            "status" => "404",
        ], 404);
        $token = substr($token, 7);
        try {
            $userInfo = Redis::get($token);
            if ($userInfo) {
                $userInfo = json_decode($userInfo);
                $request->attributes->add(['user' => $userInfo]);
                return $next($request, 200);
            }
            return response()->json([
                "massage" => "This token is not valid.",
                "status" => "401",
            ], 401);
        } catch (Throwable $e) {
            return  response()->json([
                "massage" => "This token is not valid.",
                "status" => "401",
            ], 401);
        }
    }
}
