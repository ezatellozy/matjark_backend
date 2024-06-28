<?php

namespace App\Http\Requests\Api\Provider\Product;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;
use App\Models\FeatureValue;
use App\Models\ProductDetails;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class ProductRequest extends ApiMasterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $status = isset($this->product) ? 'nullable' : 'required';

        $rules = [
            'is_active'                               => 'nullable|in:0,1',
            'size_guide'                              => 'nullable|image|mimes:jpg,jpeg,png',
            'ordering'                                => 'nullable|numeric|unique:products,ordering,'. $this->product,
            'code'                                    => 'nullable|string|min:3|max:1000|unique:products,code,'. $this->product,
            'category_ids'                            => 'required|array',
            // 'category_ids.*'                          => 'exists:categories,id,position,second_sub',
            'category_ids.*'                          => 'exists:categories,id',

            'product_details'                         => $status.'|array',
            'product_details.*.product_detail_id'     => 'nullable|exists:product_details,id,product_id,'.$this->product,
            'product_details.*.color_id'              => 'nullable|exists:colors,id',
            'product_details.*.size_id'               => 'nullable|exists:sizes,id',
            'product_details.*.code'                   => 'nullable|string|min:3|max:1000',

            'product_details.*.features'              => 'nullable|array',
            'product_details.*.features.*.feature_id' => 'exists:features,id',
            'product_details.*.features.*.value_id'   => 'exists:feature_values,id',

            'product_details.*.quantity'              => $status.'|numeric',
            'product_details.*.price'                 => $status.'|numeric',

            'product_details.*.media'                 => $status.'|array',
            'product_details.*.media.*.image_id'      => 'nullable|exists:product_media,id',
            'product_details.*.media.*.image'         => 'image|mimes:jpeg,png,jpg,gif',
        ];

        if (isset($this->product_details))
        {
            foreach($this->product_details as $product_details)
            {
                if (array_has($product_details, ['features']))
                {
                    foreach($product_details['features'] as $feature)
                    {
                        $feature_values = FeatureValue::where('feature_id', $feature['feature_id'])->find($feature['value_id']);

                        if (! $feature_values)
                        {
                            throw new HttpResponseException(response()->json([
                                'status' => 'fail',
                                'data' => null,
                                'message' => trans('dashboard.error.value_id_not_true'),
                            ], 422));
                        }
                    }
                }

                if (isset($product_details['product_detail_id']))
                {
                    $product_detail = ProductDetails::find($product_details['product_detail_id']);

                    if ($product_detail->getFlashSaleQuantity() > $product_details['quantity'])
                    {
                        throw new HttpResponseException(response()->json([
                            'status'  => 'fail',
                            'data'    => null,
                            'message' => trans('dashboard.error.quantity_not_cover_quantity_required_for_flash_sale'),
                        ], 422));
                    }
                }

                if (! isset($product_details['product_detail_id']) and ! isset($product_details['media']))
                {
                    throw new HttpResponseException(response()->json([
                        'status'  => 'fail',
                        'data'    => null,
                        'message' => trans('dashboard.error.image_required'),
                    ], 422));
                }
            }
        }

        // if ($this->category_ids)
        // {
        //     $root_id = root(Category::find($this->category_ids[0]))->id;

        //     foreach ($this->category_ids as $category_id)
        //     {
        //         $category = Category::find($category_id);

        //         if ($category and $root_id and root($category) and root($category)->id != $root_id)
        //         {
        //             throw new HttpResponseException(response()->json([
        //                 'status'  => 'fail',
        //                 'data'    => null,
        //                 'message' => trans('dashboard.error.root_not_true'),
        //             ], 422));
        //         }

        //         if (! $category)
        //         {
        //             throw new HttpResponseException(response()->json([
        //                 'status'  => 'fail',
        //                 'data'    => null,
        //                 'message' => trans('dashboard.error.category_id_not_exist'),
        //             ], 422));
        //         }

        //         $root_id = root($category)->id;
        //     }
        // }

        foreach(config('translatable.locales') as $locale)
        {
            $rules[$locale.'.name'] = $status.'|string|between:2,255';
            $rules[$locale.'.desc'] = 'nullable|string|between:2,500';
        }

        return $rules;
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();

        if (array_key_exists("category_ids",$data)) {
            $category = Category::findOrFail($data['category_ids'][0]);
        } else {
            $category = null;
        }

        $data['main_category'] = root($category);
        $data['added_by_id'] = auth('api')->id();

        if (isset($data['en']['name']))
        {
            $data['slug'] = Str::snake($data['en']['name']);
        }

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }


    public function messages()
    {

        $arr = [];

        $lang = app()->getLocale();

        if($lang == 'en') {

            if(! empty(request()->product_details)) {
                foreach(request()->product_details as $key => $key2) {
                    
                    $arr['product_details.'.$key.'.product_detail_id.exists'] = ' product detail id '  .($key + 1). ' is not found';

                    $arr['product_details.'.$key.'.color_id.exists'] = ' product color id '  .($key + 1). ' is not found';
                    $arr['product_details.'.$key.'.size_id.exists'] = ' product size id '  .($key + 1). ' is not found';
                    $arr['product_details.'.$key.'.code.digits'] = ' product code '  .($key + 1). ' must be a 4 digits';

                    $arr['product_details.'.$key.'.quantity.required'] = ' product quantity '  .($key + 1). ' is required';
                    $arr['product_details.'.$key.'.quantity.numeric'] = ' product quantity '  .($key + 1). ' must be numeric';
                    
                    $arr['product_details.'.$key.'.price.required'] = ' product price. '  .($key + 1). ' is required';
                    $arr['product_details.'.$key.'.price.numeric'] = ' product price '  .($key + 1). ' must be numeric';
                }
            }

            if(! empty(request()->category_ids)) {
                foreach(request()->category_ids as $key => $key2) {
                    
                    $arr['category_ids.'.$key.'.exists'] = ' category '  .($key + 1). ' is not found';
                }
            }


        } else {

            if(! empty(request()->product_details)) {
                foreach(request()->product_details as $key => $key2) {

                 
                    $arr['product_details.'.$key.'.product_detail_id.exists'] = ' رقم  المنتج '  .($key + 1). ' غير موجود ';
                    $arr['product_details.'.$key.'.product_detail_id.distinct'] = ' رقم  المنتج '  .($key + 1). ' لا يجب ان يحتوي علي قيم متكررة ';

                    $arr['product_details.'.$key.'.color_id.exists'] = ' لون المنتج '  .($key + 1). ' غير موجود';
                    $arr['product_details.'.$key.'.size_id.exists'] = ' مقاس المنتج '  .($key + 1). ' غير موجود';
                    $arr['product_details.'.$key.'.code.digits'] = ' كود المنتج '  .($key + 1). ' يجب ان يحتوي علي 4 حروف أو ارقام';

                    $arr['product_details.'.$key.'.quantity.required'] = ' الكمية للعميل  المنتج '  .($key + 1). ' مطلوب';
                    $arr['product_details.'.$key.'.quantity.numeric'] = ' الكمية للعميل  المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                    $arr['product_details.'.$key.'.price.required'] = ' سعر  المنتج '  .($key + 1). ' مطلوب';
                    $arr['product_details.'.$key.'.price.numeric'] = ' سعر  المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';

                }
            }

            if(! empty(request()->product_details)) {
                foreach(request()->product_details as $key => $key2) {

                 
                    $arr['category_ids.'.$key.'.exists'] = ' القسم '  .($key + 1). ' غير موجود ';

                }
            }

        }


        return $arr;
    }

}
