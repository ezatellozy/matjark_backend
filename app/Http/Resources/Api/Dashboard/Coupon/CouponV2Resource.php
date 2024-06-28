<?php

namespace App\Http\Resources\Api\Dashboard\Coupon;

use App\Models\Category;
use App\Models\OrderCoupon;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponV2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $ids = $this->apply_ids;
        $products = [];
        $categories = [];

        if (in_array($this->applly_coupon_on, ['special_products', 'except_products']))
        {
            $products = Product::whereHas('productDetails', function($query) use($ids) {
                $query->whereIn('id', $ids);
            })->get();

            $products->each(function ($product) use ($ids) {
                data_set($product, 'product_detail_ids', array_intersect($ids, $product->productDetails->pluck('id')->toArray()));
            });
        }
        elseif (in_array($this->applly_coupon_on, ['special_categories', 'except_categories']))
        {
            // $categories = Category::whereIn('id', $ids)->where('position', 'second_sub')->get();
            $categories = Category::whereIn('id', $ids)->get();

            $categories->each(function ($category) {
                data_set($category, 'root', root($category));
            });
        }
        return [
            'id' => (int)$this->id,
            'image' => (string)$this->image,
            'code' => (string)$this->code,
            'start_at' => $this->start_at ? $this->start_at->format('Y-m-d') : null,
            'end_at' => $this->end_at ? $this->end_at->format('Y-m-d') : null,
            'start_time' => $this->start_at ? $this->start_at->format('H:i') : null,
            'end_time' => $this->end_at ? $this->end_at->format('H:i') : null,
            'is_active' => (bool)$this->is_active,
            'discount_type' => (string)$this->discount_type,
            'discount_amount' => (double)$this->discount_amount,
            'max_discount' => (int)$this->max_discount,
            'max_used_num' => (int)$this->max_used_num,
            'max_used_for_user' => (int)$this->max_used_for_user,
//            'num_of_used' => (int)$this->num_of_used,
            'num_of_used' => OrderCoupon::where('coupon_id', $this->id)->whereHas('order', function ($q) {
                $q->where('status', 'admin_delivered');
            })->count(),
            'remain_used' => $this->max_used_num - $this->num_of_used,
            'addtion_options' => (string)$this->addtion_options,
            'applly_coupon_on' => (string)$this->applly_coupon_on,
            'apply_on_product' => CouponProductResource::collection($products),
            'applly_on_category' => CouponCategoryResource::collection($categories),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'order_count_use_coupon' => OrderCoupon::where('coupon_id', $this->id)->whereHas('order', function ($q) {
                $q->where('status', 'pending');
            })->count(),
        ];

        // return [
        //     'id'                 => (int) $this->id,
        //     'image'              => (string) $this->image,
        //     'code'               => (string) $this->code,
        //     'start_at'           => $this->start_at ? $this->start_at->format('Y-m-d') : null,
        //     'end_at'             => $this->end_at ? $this->end_at->format('Y-m-d') : null,
        //     'start_time'           => $this->start_at ? $this->start_at->format('H:i') : null,
        //     'end_time'             => $this->end_at ? $this->end_at->format('H:i') : null,
        //     'is_active'          => (bool) $this->is_active,
        //     'discount_type'      => (string) $this->discount_type,
        //     'discount_amount'    => (double) $this->discount_amount,
        //     'max_discount'       => (int) $this->max_discount,
        //     'max_used_num'       => (int) $this->max_used_num,
        //     'max_used_for_user'  => (int) $this->max_used_for_user,
        //     'num_of_used' => (int)$this->num_of_used,
        //     'remain_used' => $this->max_used_num - $this->num_of_used,
        //     'addtion_options'    => (string) $this->addtion_options,
        //     'applly_coupon_on'   => (string) $this->applly_coupon_on,
        //     'apply_on_product'   => CouponProductResource::collection($products),
        //     'applly_on_category' => CouponCategoryResource::collection($categories),
        //     'created_at'         => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        //     'order_count_use_coupon' => OrderCoupon::where('coupon_id', $this->id)->whereHas('order', function ($q) {
        //         $q->where('status', 'admin_delivered');
        //     })->count(),
        // ];
    }
}
