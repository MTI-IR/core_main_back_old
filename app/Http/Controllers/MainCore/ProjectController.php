<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\ProjectResource;
use App\Http\Resources\MainCore\ProjectsResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use PhpParser\Node\Stmt\ElseIf_;
use Spatie\Permission\Contracts\Permission;
use Throwable;

class ProjectController extends Controller
{
    function projectFinder(
        $permission_id,
        $category_id = '',
        $sub_category_id = '',
        $state_id = '',
        $city_id = '',
    ) {
        return (Project::where('permission_id', $permission_id)
            ->where('show_time', '<=', now())
            ->where("category_id", "like", $category_id . "%")
            ->where("sub_category_id", "like", $sub_category_id . "%")
            ->where("city_id", "like", $city_id . "%")
            ->where("state_id", "like", $state_id . "%")
            ->orderBy('show_time')->get());
    }
    public function index(Request $request)
    {
        // $category_id = $request->category_id;
        // $sub_category_id = $request->sub_category_id;
        // $searchBy = 'all';
        // $searchValue = null;
        $category_id = $request->category_id ? $request->category_id : '';
        $sub_category_id = $request->sub_category_id ? $request->sub_category_id : '';
        $state_id = $request->state_id ? $request->state_id : '';
        $city_id = $request->city_id ? $request->city_id : '';
        // if ($category_id != null) {
        //     $searchBy = 'category_id';
        // }
        // if ($sub_category_id != null) {
        //     $searchBy = 'sub_category_id';
        //     $searchValue = $sub_category_id;
        // }
        $user = $request->get('user');
        if (!$user) {
            $auth = $request->header('authorization');
            if ($auth) {
                $token = $auth;
                if ($token) {
                    $token = substr($token, 7);
                    $userInfo = Redis::get($token);
                    if ($userInfo) {
                        $user = json_decode($userInfo);
                    }
                }
            }
        }
        if (!$user) {
            $basePermission = DB::table('permissions')->where('name', 'base')->first();
            $projects = [];
            // $baseProjects = $this->projectFinder($basePermission->id, $searchBy, $searchValue);
            $baseProjects = $this->projectFinder($basePermission->id, $category_id, $sub_category_id, $state_id, $city_id);
            foreach ($baseProjects as $project) {
                $images = [];
                foreach ($project->images as $image) {
                    $images[$image->priority] = $image->url;
                }
                $project->images = $images;
                $project->permission_name = $basePermission->name;
                // global $projects[];
                $projects[] = $project;
            };
            return new ProjectsResource($projects);
        } else {
            $user_permissions = collect($user->all_permissions)->sortBy('priority');
            $permisions = DB::table('permissions')->orderBy('priority')->get();
            if (!$user_permissions->count()) {
                $user_permissions[0] = DB::table('permissions')
                    ->orderBy('priority')->first();
            }
            $priority = $user_permissions[0]->priority;
            $projects = [];
            foreach ($permisions as $permission) {
                if ($permission->priority <= $priority) {
                    // $userProjects = $this->projectFinder($permission->id, $searchBy, $searchValue);
                    $userProjects = $this->projectFinder($permission->id, $category_id, $sub_category_id, $state_id, $city_id);
                    foreach ($userProjects as $project) {
                        $images = [];
                        foreach ($project->images as $image) {
                            $images[$image->priority] = $image->url;
                        }
                        $project->images = $images;
                        $project->permission_name = $permission->name;
                        $projects[] = $project;
                    };
                }
                return new ProjectsResource($projects);
            }
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            if ($project->show_time > now())
                return response()->json([
                    "massage" => "This project is not available for now!",
                    "status" => 403,
                ], 403);
            $images = [];
            foreach ($project->images as $image) {
                $images[$image->priority] = $image->url;
            }
            $project->permission_name = DB::table('permissions')
                ->where('id', $project->permission_id)
                ->first()->name;
            $project->images = $images;
            return new ProjectResource($project);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "There is no project whit this ID ",
                "status" => 404,
            ], 404);
        }
    }
}
