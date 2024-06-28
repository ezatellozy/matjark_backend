<?php

namespace App\Http\Resources\Api\Website\CommonQuestion;

use Illuminate\Http\Resources\Json\JsonResource;

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
        // $locales = [];

        // foreach (config('translatable.locales') as $locale) {
        //     $locales[$locale]['question'] = $this->translate($locale)->question;
        //     $locales[$locale]['answer'] = $this->translate($locale)->answer;
        // }

        return [
            'id'             => (int) $this->id,
            'question' => $this->question,
            'answer' => $this->answer
        ] 
        // + $locales
        ;
    }
}