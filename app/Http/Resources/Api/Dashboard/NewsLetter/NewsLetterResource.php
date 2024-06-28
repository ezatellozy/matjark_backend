<?php

namespace App\Http\Resources\Api\Dashboard\NewsLetter;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsLetterResource extends JsonResource
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
            'id'         => $this->id,
            'email'       => $this->email,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        ];
    }
}
