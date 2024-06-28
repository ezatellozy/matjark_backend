<?php

namespace App\Http\Resources\Api\Dashboard\Product;

use App\Http\Resources\Api\App\Category\CategoryItemResource;
use App\Models\ProductMedia;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\CommonQuestion\SimpleCommonQuestionResource;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = ProductMedia::where(['product_id' => $this->id, 'option' => null])->where('product_details_id','!=',null)->orderBy('created_at','asc')->first();

        return [
            'id'         => (int) $this->id,
            'image'      => asset('storage/images/products/'.$this->id.'/'.$image->product_details_id.'/'.$image->media),
            'name'       => (string) $this->name,
            'slug'       => (string) $this->slug,
            'code'    => (string) $this->code,
            'desc'       => (string) $this->desc,
            'rate_avg'   => (double) $this->rate_avg,
            'ordering'   => (int) $this->ordering,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'common_questions' => SimpleCommonQuestionResource::collection($this->commonQuestions),
            'is_active'   => (bool) $this->is_active,

            'main_category' => CategoryItemResource::make($this->main_category),
        ];
    }
}
