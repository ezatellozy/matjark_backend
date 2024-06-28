<?php

namespace App\Http\Resources\Api\Provider\Category;

use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $third_level_ids = allThirdLavels($this->resource) ? allThirdLavels($this->resource)->pluck('id')->toArray() : [$this->id];

        $products_count = Product::whereHas('categories', function($query) use($third_level_ids) {
            $query->whereIn('category_id', $third_level_ids);
        })->count();

        return [
            'id'             => (int) $this->id,
            // 'parent_id'      => $this->parent_id ? (int) $this->parent_id : null,
            'main_category'  => new ParentCategoryResource(root($this->resource)),
            'parent'         => $this->parent ? new ParentCategoryResource($this->parent) : null,
            'name'           => (string) $this->name,
            'slug'           => (string) $this->slug,
            'desc'           => (string) $this->desc,
            'image'          => $this->image,
            'is_active'      => (bool) $this->is_active,
            'ordering'       => (int) $this->ordering,
            'position'       => (string) $this->position,
            'children_count' => (int) count($this->children),
            'products_count' => (int) $products_count,
        ];
    }
}
