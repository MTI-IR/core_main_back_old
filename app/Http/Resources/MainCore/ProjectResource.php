<?php

namespace App\Http\Resources\MainCore;

use App\Models\Company;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $summary = $this->summary;
        if ($summary == null) {
            $summary = substr($this->description, 100) . "...";
        }
        $company_to_return = null;
        if ($this->company) {
            $company = $this->company;
            $company_to_return = [
                "id" => $company->id,
                "name" => $company->name,
                "image_url" => $company->images(),
            ];
        }
        $user = $this->user;
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "our_review" => $this->our_review,
            "state_name" => $this->state_name,
            "city_name" => $this->city_name,
            "summary" => $summary,
            "show_time" => $this->show_time,
            "user" => [
                "id" => $user->id,
                "name" => $user->first_name . " " . $user->last_name,
                "image_url" => $user->images(),
            ],
            "company" => $company_to_return,
            "tags_name" => Tag::findOrFail($this->tag_id)->name,
            "images" => $this->images,
            "permission_name" => $this->permission_name,
            "tiket" => $this->tiket,
            "mark" => $this->mark,
        ];
    }
}
