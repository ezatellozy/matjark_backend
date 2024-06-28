<?php

namespace App\Http\Resources\Api\Dashboard\Report;

use App\Http\Resources\Api\App\Category\CategoryItemResource;
use App\Http\Resources\Api\App\Category\CategoryResource;
use App\Http\Resources\Api\Dashboard\City\CityItemResource;
use App\Http\Resources\Api\Dashboard\City\CityResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Dashboard\Meta\MetaResource;
use App\Http\Resources\Api\Dashboard\Product\ProductItemResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductDetails;

class SalesCitiesReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        
        $addressesArr = $this->addresses()->pluck('id')->toArray();

        $orderArr = Order::whereIn('address_id',$addressesArr)->pluck('id')->toArray();
        
        $total_qty = OrderProduct::whereHas('order',function($order) use($orderArr) {

            $order->where('status', 'admin_delivered')->where('is_payment','paid')->whereIn('order_id',$orderArr);
            
        })->sum('quantity');
        
        $total_price = OrderProduct::whereHas('order',function($order) use($orderArr,$request) {

            $order->where('is_payment','paid')->where('status', 'admin_delivered')->whereIn('order_id',$orderArr)
            
            ->when($request->from && $request->to, function ($query) use ($request) {
    
                $query->whereBetween('created_at', [$request->from, $request->to]);
    
            })->when($request->from, function ($query) use ($request) {
    
                $query->whereDate('created_at','>=',$request->from);
    
            })->when($request->to, function ($query) use ($request) {
    
                $query->whereDate('created_at','<=',$request->to);
            });
            
        })->sum('total_price');

        $order_products = OrderProduct::whereHas('order',function($order) use($orderArr,$request) {

            $order->where('is_payment','paid')->whereIn('order_id',$orderArr)
            
            ->when($request->from && $request->to, function ($query) use ($request) {
    
                $query->whereBetween('created_at', [$request->from, $request->to]);
    
            })->when($request->from, function ($query) use ($request) {
    
                $query->whereDate('created_at','>=',$request->from);
    
            })->when($request->to, function ($query) use ($request) {
    
                $query->whereDate('created_at','<=',$request->to);
            });
            
        })->get();

        $total_offer_price = 0;

        foreach($order_products as $order_product) {

            if($order_product->flash_sale_price != null) {

                $total_offer_price = $total_offer_price + ($order_product->total_product_price_before - $order_product->flash_sale_price);

            } elseif($order_product->offer_price != null) {

                $total_offer_price = $total_offer_price + ($order_product->total_product_price_before - $order_product->offer_price);
            }
        }

        return [
            'id'                 => $this->id,
            'city'               => CityItemResource::make($this),
            'total_qty'          => $total_qty,
            'total_product_price_before'  => $total_price - $total_offer_price,
            'total_offer_price'  => $total_offer_price,
            'total_price'        => $total_price,
        ];
    }
}
