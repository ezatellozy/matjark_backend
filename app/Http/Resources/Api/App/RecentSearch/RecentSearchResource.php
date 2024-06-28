<?php

namespace App\Http\Resources\Api\App\RecentSearch;

use Illuminate\Http\Resources\Json\JsonResource;

class RecentSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'created_at' => (string) $this->created_at->format('Y-m-d'),
            'created_time' => (string) $this->created_at->format('H:i a'),
        ];
    }
}
