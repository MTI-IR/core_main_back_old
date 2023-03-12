<?php

namespace App\Http\Resources\MainCore;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProjectResource extends JsonResource
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
        $tag = $this->tag_id;
        if ($tag) {
            $tag = Tag::findOrFail($this->tag_id)->name;
        }
        $user = $this->user;
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "our_review" => $this->our_review,
            "state_name" => $this->state_name,
            "city_name" => $this->city_name,
            "category_name" => $this->category_name,
            "sub_category_name" => $this->sub_category_name,
            "company_name" => $this->company_name,
            "state_id" => $this->state_id,
            "city_id" => $this->city_id,
            "category_id" => $this->category_id,
            "sub_category_id" => $this->sub_category_id,
            "company_id" => $this->company_id,
            "summary" => $summary,
            "show_time" => $this->show_time,
            "user" => [
                "id" => $user->id,
                "name" => $user->first_name . " " . $user->last_name,
                "image_url" => $user->images(),
            ],
            "company" => $company_to_return,
            "tags_name" => $tag,
            "images" => $this->images,
            "price" => $this->price,
            "edit_able" => $this->edit_able,
        ];
    }
}
