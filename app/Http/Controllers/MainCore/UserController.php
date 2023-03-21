<?php

namespace App\Http\Controllers\MainCore;

use App\Http\Controllers\Controller;
use App\Http\Resources\MainCore\ProjectsResource;
use App\Http\Resources\MainCore\SiteInfoResource;
use App\Http\Resources\mainCore\userInfoResource;
use App\Models\Image;
use App\Models\Mark;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Redis;
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
                "message" => "no project with this ID",
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
            "message" => "project marked",
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
                "message" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $mark  = Mark::where("user_id", $user->id)->where("project_id", $project_id)->first();
        if ($mark) {
            $mark->delete();
        }
        return response()->json([
            "message" => "project unmarked",
            "status" => "200",
        ], 200);
    }




    public function ticket(Request $request)
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
                "message" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $ticketExist  = count(Ticket::where("user_id", $user->id)->where("project_id", $project_id)->get()) ? true : false;
        if (!$ticketExist) {
            $ticket = new Ticket();
            $ticket->user_id = $user->id;
            $ticket->project_id = $project_id;
            $ticket->save();
        }
        return response()->json([
            "message" => "ticket added",
            "status" => "200",
        ], 200);
    }


    public function unTicket(Request $request)
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
                "message" => "no project with this ID",
                "status" => "404"
            ], 404);
        }
        $ticket = Ticket::where("user_id", $user->id)->where("project_id", $project_id)->first();
        if ($ticket) {
            $ticket->delete();
        }
        return response()->json([
            "message" => "ticket removed",
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


    public function ticketProjects(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);

        $projects = [];
        $ticketProjects = $user->ticketProjects;
        if ($ticketProjects) {
            foreach ($ticketProjects as $project) {
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

    public function tickets(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);
        $tickets = $user->tickets;
        foreach ($tickets as $ticket) {
            $ticket->project = $ticket->project;
        }
        return new ticketsResource($tickets);
    }
    public function userInfo(Request $request)
    {
        $userRedis = $request->get("user");
        $user = User::findOrFail($userRedis->id);
        return new userInfoResource($user);
    }
    public function editInfo(Request $request)
    {
        $data = $request->validate([
            "first_name" => "required|string",
            "last_name" => "required|string",
            "national_code" => "required|digits:10",
            "email" => "required|email",
        ]);
        try {
            $userRedis = $request->get("user");
            $user = User::findOrFail($userRedis->id);
            $user->first_name = $data["first_name"];
            $user->last_name = $data["last_name"];
            $user->email = $data["email"];
            if ($data["national_code"] && !$user->validate)
                $user->national_code = $data["national_code"];
            // $user->validate = false;
            $newImage = $request->file("image");
            if ($newImage) {
                $images = Image::where('imageable_id', $user->id)->where('imageable_type', 'App\Models\User');
                $images = $images->get();
                foreach ($images as $image) {
                    $image->delete();
                    if (file_exists(public_path('images') . substr($image->url, 28))) {
                        FacadesFile::delete(public_path('images') . substr($image->url, 28));
                    }
                }
                $filename = time() . $user->id . '.' . $newImage->getClientOriginalExtension();
                $newImage->move(public_path('images'), $filename);
                $image_url = "http://localhost:8000/images/" . $filename;
                $image = $user->images()->make();
                $image->priority = 0;
                $image->url = $image_url;
                $image->save();
                $userRedis->images = [$image_url];
                $userRedis->validate = $user->validate;
                Redis::setex($userRedis->token, 43200,  json_encode($userRedis));
            }
            $user->save();
            return response()->json([
                "message" => "user edited",
                "status" => "200",
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                "message" => "User not found",
                "status" => "404",
            ], 404);
        }
    }

    public function companies(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);
        $companies = $user->companies;
        return new SiteInfoResource($companies);
    }
    public function canCreateProject(Request $request)
    {
        $user = $request->get("user");
        $user = User::findOrFail($user->id);
        if ($user->validate) {
            return response()->json([
                'message' => "This user can create project",
                "status" => '200'
            ], 200);
        }
        return response()->json([
            'message' => "This user can not create project",
            "status" => '403'
        ], 403);
    }
}
