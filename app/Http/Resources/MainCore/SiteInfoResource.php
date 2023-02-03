<?php

namespace App\Http\Resources\MainCore;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $toReturn = [];
        foreach ($this->resource as $item) {
            $toReturn[$item->name] = [
                "id" => $item->id,
                "name" => $item->name,
            ];
        }
        return $toReturn;
    }
}
