<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductMedia;

class ProductObserver
{
    public function saved(Product $product)
    {
        if (request()->hasFile('size_guide')) {
            if ($product->media()->where('option','size_guide')->exists()) {
                $image = ProductMedia::where(['option' => 'size_guide','product_id' => $product->id ,'media_type' => 'image'])->first();
                $image->delete();
                if (file_exists(storage_path('app/public/images/products/'.$product->id.'/'.$image->media))){
                    \File::delete(storage_path('app/public/images/products/'.$product->id .'/'.$image->media));
                    $image->delete();
                }
            }

            $image = uploadImg(request()->size_guide, 'products/'.$product->id);
            $product->media()->create(['media' => $image,'media_type' => 'image' ,'option' =>'size_guide', 'alt_ar' => request('image_alt_ar'), 'alt_en' => request('image_alt_en')]);
        }
    }

}
