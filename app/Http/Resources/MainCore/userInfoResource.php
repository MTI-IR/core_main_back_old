<?php

namespace App\Http\Resources\mainCore;

use Illuminate\Http\Resources\Json\JsonResource;

class userInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "national_code" => $this->national_code,
            "phone_number" => $this->phone_number,
            "email" => $this->email,
        ];
    }
}
