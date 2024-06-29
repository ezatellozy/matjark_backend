<?php

namespace App\Http\Resources\Api\App\Product;

use App\Http\Resources\Api\App\Help\{ColorResource, SizeResource};
use App\Models\FavouriteProduct;
use App\Models\OrderRate;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth()->guard('api')->user();
        $fav = false;

        if ($user) {
            $fav = FavouriteProduct::where([
                'user_id' => auth('api')->id(),
                'guest_token' => null,
                'product_detail_id' => $this->id
            ])->exists();
        } elseif ($request->guest_token) {
            $fav = FavouriteProduct::where([
                'guest_token' => $request->guest_token,
                'product_detail_id' => $this->id,
                'user_id' => null
            ])->exists();
        }

        return [
            'id' => (int)$this->id,
            // 'color' => new ColorResource($this->color), // Uncomment if needed
            'size' => new SizeResource($this->size),
            'price' => (float)$this->price,
            'currency' => 'SAR',
            'rate_avg' => (float)$this->rate_avg,
            'num_of_reviews' => (int)OrderRate::where('product_detail_id', $this->id)->count(),
            'have_sale' => (bool)$this->have_sale,
            'quantity' => (float)$this->quantity,
            'code' => (string)$this->code,
            'images' => $this->images, // Make sure $this->images is an array or cast it if needed
            'is_fav' => $fav,
            'features' => $this->features ? ProductFeaturesResource::collection($this->features) : null,
        ];
    }
}