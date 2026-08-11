<?php

namespace App\Http\Resources\Res;

use Illuminate\Http\Resources\Json\JsonResource;

class OfficerResource extends JsonResource
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
            'register_number' => $this->register_number,
            'class' => $this->class,
            'name' => [
                'full_name' => $this->full_name,
                'split_name' => [
                    'first_title' => $this->first_title,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'last_title' => $this->last_title,
                ],
            ],
            'rank' => [
                'id' => $this->rank->id ?? null,
                'emp_id' => $this->rank->emp_id ?? null,
                'name' => $this->rank->name ?? null,
                'full_name' => $this->rank->full_name ?? null,
                'employment_type' => [
                    'name' => $this->rank->employmentType->name ?? null,
                ],
            ],
            'position' => [
                'id' => $this->position->id ?? null,
                'name' => $this->position->name ?? null,
            ],
            'police' => [
                'id' => $this->police->id ?? null,
                'spptti_id' => $this->police->spptti_id ?? null,
                'name' => $this->police->name ?? null,
                'class' => $this->police->class ?? null,
            ]
        ];
    }
}
