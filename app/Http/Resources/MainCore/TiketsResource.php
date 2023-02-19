<?php

namespace App\Http\Resources\MainCore;

use Illuminate\Http\Resources\Json\JsonResource;

class TiketsResource extends JsonResource
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
        $data = $this->resource;
        foreach ($data as $tiket) {
            $toReturn[] = [
                "id" => $tiket->id,
                "created_at" => $tiket->created_at,
                "project" => $tiket->project,
            ];
        }
        return $toReturn;
    }
}
