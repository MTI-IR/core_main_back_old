<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\UserInfoResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Throwable;

class LoginController extends Controller
{
    public function Login(Request $request)
    {
        // dd($request);
        $data = $request->validate([
            "phone_number"     =>  "required|string",
            "password"  =>  "required|string|max:32"
        ]);
        $user    =   User::where('phone_number', '=',  $data["phone_number"])->first();
        // $user    =   DB::table('users')->select('id', 'name', 'email', 'phone_number', 'password')->where('email', '=',  $data["email"])->first();
        if (!$user) {
            return response()->json([
                "error" => "username or password is incorrect"
            ], 404);
        }
        if (Hash::check($data["password"], $user->password)) {
            $userInfo = [
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "phone_number" => $user->phone_number,
                "host" => $request->header()['host'][0],
                "login_time" => Carbon::now(),
                'ip' => $request->ip(),
                "permissions_via_roles" => $user->getPermissionsViaRoles(),
                "all_permissions" => $user->getAllPermissions(),
                "direct_permissions" => $user->getDirectPermissions(),
            ];
            $uuid = Str::uuid();
            $userId = $user->id . "";
            $i = Str::length($userId);
            for ($i; $i < 6; $i++) {
                $userId = '0' . $userId;
            }
            $sessions  = Redis::keys($userId . "*");
            $session = 0;
            // dd($sessions);
            $sessonsCount = count($sessions);
            for ($i = 0; $i < $sessonsCount; $i++) {
                if ($i != (int) substr($sessions[$i], 54, 6)) {
                    $session = $i;
                    break;
                }
            }
            $session = ($session ? $session : $sessonsCount) . "";
            $i = Str::length($session);
            for ($i; $i < 6; $i++) {
                $session = '0' . $session;
            }
            $token =  $userId  . '-' . $session . '-' .  $uuid;
            Redis::setex($token, 43200,  json_encode($userInfo));
            return new LoginResource([
                "token" => $token,
                "user" => $userInfo,
            ]);
        } else {
            return response()->json([
                "error" => "username or password is incorrect"
            ], 404);
        }
    }
    public function userInfo(Request $request)
    {
        $auth = $request->header('authorization');
        try {
            $token = substr($auth, 7);
            $userInfo = Redis::get($token);
            $userInfo = json_decode($userInfo);
            if ($userInfo)
                return new UserInfoResource($userInfo);
            else
                return response()->json([
                    "massage" => "This token is not valid.",
                    "status" => "401",
                ], 401);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "This token is not valid.",
                "status" => "401",
            ], 401);
        }
    }
}
