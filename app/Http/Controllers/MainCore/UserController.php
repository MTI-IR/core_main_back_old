<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\ProjectsResource;
use App\Http\Resources\MainCore\SiteInfoResource;
use App\Http\Resources\MainCore\TiketsResource;
use App\Models\Mark;
use App\Models\Project;
use App\Models\Tiket;
use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    public function mark(Request $request)
    {
        $data = $request->validate([
            "project_id" => "required|string"
        ]);
        $user = $request->get("user");
        $project_id = $data["project_id"];
        try {
            Project::findOrFail($project_id);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $markExist  = count(Mark::where("user_id", $user->id)->where("project_id", $project_id)->get()) ? true : false;
        if (!$markExist) {
            $mark = new Mark();
            $mark->user_id = $user->id;
            $mark->project_id = $project_id;
            $mark->save();
        }
        return response()->json([
            "massage" => "project marked",
            "status" => "200",
        ], 200);
    }


    public function unMark(Request $request)
    {
        $data = $request->validate([
            "project_id" => "required|string"
        ]);
        $user = $request->get("user");
        $project_id = $data["project_id"];
        try {
            Project::findOrFail($project_id);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $mark  = Mark::where("user_id", $user->id)->where("project_id", $project_id)->first();
        if ($mark) {
            $mark->delete();
        }
        return response()->json([
            "massage" => "project unmarked",
            "status" => "200",
        ], 200);
    }




    public function tiket(Request $request)
    {
        $data = $request->validate([
            "project_id" => "required|string"
        ]);
        $user = $request->get("user");
        $project_id = $data["project_id"];
        try {
            Project::findOrFail($project_id);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $tiketExist  = count(Tiket::where("user_id", $user->id)->where("project_id", $project_id)->get()) ? true : false;
        if (!$tiketExist) {
            $tiket = new Tiket();
            $tiket->user_id = $user->id;
            $tiket->project_id = $project_id;
            $tiket->save();
        }
        return response()->json([
            "massage" => "tiket added",
            "status" => "200",
        ], 200);
    }


    public function unTiket(Request $request)
    {
        $data = $request->validate([
            "project_id" => "required|string"
        ]);
        $user = $request->get("user");
        $project_id = $data["project_id"];
        try {
            Project::findOrFail($project_id);
        } catch (Throwable $e) {
            return response()->json([
                "massage" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $tiket = Tiket::where("user_id", $user->id)->where("project_id", $project_id)->first();
        if ($tiket) {
            $tiket->delete();
        }
        return response()->json([
            "massage" => "tiket removed",
            "status" => "200",
        ], 200);
    }

    public function markProjects(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);

        $projects = [];
        $markProjects = $user->markProjects;
        if ($markProjects) {
            foreach ($markProjects as $project) {
                $images = [];
                foreach ($project->images as $image) {
                    $images[$image->priority] = $image->url;
                }
                $project->images = $images;
                $projects[] = $project;
            };
        }
        return new ProjectsResource($projects);
    }


    public function tiketProjects(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);

        $projects = [];
        $tiketProjects = $user->tiketProjects;
        if ($tiketProjects) {
            foreach ($tiketProjects as $project) {
                $images = [];
                foreach ($project->images as $image) {
                    $images[$image->priority] = $image->url;
                }
                $project->images = $images;
                $projects[] = $project;
            };
        }
        return new ProjectsResource($projects);
    }

    public function tikets(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);
        $tikets = $user->tikets;
        foreach ($tikets as $tiket) {
            $tiket->project = $tiket->project;
        }
        return new TiketsResource($tikets);
    }

    public function companies(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);
        $companies = $user->companies;
        return new SiteInfoResource($companies);
    }
}
