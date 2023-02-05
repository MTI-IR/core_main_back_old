<?php

namespace App\Http\Resources\MainCore;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $data =  parent::toArray($request);
        $toReturn = [];

        $data = $this->resource;
        foreach ($data as $project) {
            $summary = $project->summary;
            if ($summary == null) {
                $summary = substr($project->description, 100) . "...";
            }
            $toReturn[] = [
                "id" => $project->id,
                "title" => $project->title,
                "state_name" => $project->state_name,
                "city_name" => $project->city_name,
                "state_name" => $project->state_name,
                "city_id" => $project->city_id,
                "state_id" => $project->state_id,
                "category_name" => Category::findOrFail($project->category_id)->name,
                "sub_category_name" => SubCategory::findOrFail($project->sub_category_id)->name,
                "category_id" => $project->category_id,
                "sub_category_id" => $project->sub_category_id,
                "description" => $project->description,
                "images" => $project->images,
                "permission_name" => $project->permission_name,
                "show_time" => $project->show_time,
            ];
        };
        return $toReturn;
    }
}
