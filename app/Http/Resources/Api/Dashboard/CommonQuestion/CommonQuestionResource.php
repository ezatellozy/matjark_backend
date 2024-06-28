<?php

namespace App\Http\Resources\Api\Dashboard\CommonQuestion;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductResource ;

class CommonQuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $locales = [];

        foreach (config('translatable.locales') as $locale) {
            $locales[$locale]['question'] = $this->translate($locale)->question;
            $locales[$locale]['answer'] = $this->translate($locale)->answer;
        }

        return [
            'id'             => (int) $this->id,
            'product'        => SimpleProductResource::make($this->product)
        ] + $locales;
    }
}