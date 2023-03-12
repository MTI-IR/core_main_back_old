<?php

namespace App\Http\Resources\MainCore;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketsResource extends JsonResource
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
        foreach ($data as $ticket) {
            $toReturn[] = [
                "id" => $ticket->id,
                "created_at" => $ticket->created_at,
                "project" => $ticket->project,
            ];
        }
        return $toReturn;
    }
}
