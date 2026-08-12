<?php

namespace App\Http\Resources\Lib;

use Illuminate\Http\Resources\Json\JsonResource;

class PoliceResource extends JsonResource
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
            'id' => $this->id,
            'class' => $this->class,
            'name' => $this->name,
            'full_name' => $this->full_name,
            'spptti_id' => $this->spptti_id,
            'address' => $this->address,
        ];
    }
}
