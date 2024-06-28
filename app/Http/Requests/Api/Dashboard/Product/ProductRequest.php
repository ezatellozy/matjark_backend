<?php

namespace App\Http\Requests\Api\Dashboard\Product;

use App\Http\Requests\Api\ApiMasterRequest;
use App\Models\Category;
use App\Models\FeatureValue;
use App\Models\ProductDetails;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        $id = ($this->has('product'))? $this->product: '';

        $product = isset($this->product)? Product::find($this->product):null;

        $meta_id = (isset($this->product) && ($product->metas()->count()))?$product->metas()->first()->id:null;

        $rules = [
            'is_active'                               => 'nullable|in:0,1',
            'size_guide'                              => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'ordering'                                => 'nullable|numeric|unique:products,ordering,'. $this->product,
            'code'                                    => 'nullable|string|min:3|max:1000|unique:products,code,'. $this->product,
            'category_ids'                            => 'required|array',
            // 'category_ids.*'                          => 'exists:categories,id,position,second_sub',
            'category_ids.*'                          => 'exists:categories,id',
            // 'media'                         => $status.'|array',
            // 'media.*.image_alt_ar'         => 'nullable|string',
            // 'media.*.image_alt_en'         => 'nullable|string',

            'product_details'                         => $status.'|array',
            'product_details.*.product_detail_id'     => 'nullable|exists:product_details,id,product_id,'.$this->product,
            'product_details.*.color_id'              => 'nullable|exists:colors,id|distinct',
            'product_details.*.code'                   => 'nullable|digits:4',

            'product_details.*.features'              => 'nullable|array',
            'product_details.*.features.*.feature_id' => 'exists:features,id',
            'product_details.*.features.*.value_id'   => 'exists:feature_values,id',

            'product_details.*.sizes.*.size_id'               => 'nullable|exists:sizes,id',
            'product_details.*.sizes.*.quantity'              => $status.'|numeric|digits_between:1,10',
            'product_details.*.sizes.*.price'                 => $status.'|numeric|digits_between:1,10',

            'product_details.*.media'                 => $status.'|array',
            'product_details.*.media.*.image_id'      => 'nullable|exists:product_media,id',
            'product_details.*.media.*.image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'product_details.*.media.*.image_alt_ar'         => 'nullable|string',
            'product_details.*.media.*.image_alt_en'         => 'nullable|string',
            // 'meta_canonical_tag'                             => 'nullable|url',
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

                // not completed yet

                // if (isset($product_details['product_detail_id']))
                // {
                //     $product_detail = ProductDetails::find($product_details['product_detail_id']);

                //     if ($product_detail->getFlashSaleQuantity() > $product_details['quantity'])
                //     {
                //         throw new HttpResponseException(response()->json([
                //             'status'  => 'fail',
                //             'data'    => null,
                //             'message' => trans('dashboard.error.quantity_not_cover_quantity_required_for_flash_sale'),
                //         ], 422));
                //     }
                // }

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
            $rules[$locale.'.name'] = $status.'|string';
            // $rules[$locale.'.keywords'] = $status.'|string';
            $rules[$locale.'.slug'] = [$status, 'string',  Rule::unique('product_translations', 'slug')->ignore($this->product, 'product_id')];
            $rules[$locale.'.desc'] = 'nullable|string';
            $rules[$locale . '.meta_tag'] = [$status,'string', Rule::unique('meta_translations','meta_tag')->ignore($meta_id, 'meta_id')];
            $rules[$locale . '.meta_title'] = [$status, 'string', Rule::unique('meta_translations','meta_title')->ignore($meta_id,'meta_id')];
            $rules[$locale . '.meta_description'] = $status.'|string';
            $rules[$locale . '.meta_canonical_tag'] = 'nullable';
            // $rules[$locale . '.meta_canonical_tag'] = 'nullable|url';

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

        // if (isset($data['en']['name']))
        // {
        //     $data['slug'] = Str::snake($data['en']['name']);
        // }

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
                    $arr['product_details.'.$key.'.code.digits'] = ' product code '  .($key + 1). ' must be a 4 digits';

                    foreach(request()->product_details[$key]['sizes'] as $size_id => $sizeArr) {

                        $arr['product_details.'.$key.'sizes.'.$size_id.'.size_id.exists'] = ' product size id '  .($key + 1). ' is not found';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.quantity.required'] = ' product quantity '  .($key + 1). ' is required';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.quantity.numeric'] = ' product quantity '  .($key + 1). ' must be numeric';

                        $arr['product_details.'.$key.'sizes.'.$size_id.'.price.required'] = ' product price. '  .($key + 1). ' is required';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.price.numeric'] = ' product price '  .($key + 1). ' must be numeric';

                        $arr['product_details.'.$key.'sizes.'.$size_id.'.quantity.digits_between'] = ' product quantity '  .($key + 1). ' must contain digits numbers between 1 , 10';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.price.digits_between'] = ' product price '  .($key + 1). ' must contain digits numbers between 1 , 10';

                    }
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

                    $arr['product_details.'.$key.'.code.digits'] = ' كود المنتج '  .($key + 1). ' يجب ان يحتوي علي 4 حروف أو ارقام';

                    $arr['product_details.'.$key.'.color_id.exists'] = ' لون المنتج '  .($key + 1). ' غير موجود';

                    foreach(request()->product_details[$key]['sizes'] as $size_id => $sizeArr) {

                        $arr['product_details.'.$key.'sizes.'.$size_id.'.size_id.exists'] = ' مقاس المنتج '  .($key + 1). ' غير موجود';

                        $arr['product_details.'.$key.'sizes.'.$size_id.'.quantity.required'] = ' الكمية للعميل  المنتج '  .($key + 1). ' مطلوب';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.quantity.numeric'] = ' الكمية للعميل  المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.quantity.digits_between'] = ' كمية المنتج '  .($key + 1). ' يجب أن يحتوي علي رقم يتكون من 1 الي 10 رقمًا/أرقام';

                        $arr['product_details.'.$key.'sizes.'.$size_id.'.price.required'] = ' سعر  المنتج '  .($key + 1). ' مطلوب';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.price.numeric'] = ' سعر  المنتج '  .($key + 1). ' يجب ان يحتوي علي ارقام';
                        $arr['product_details.'.$key.'sizes.'.$size_id.'.price.digits_between'] = ' سعر  المنتج '  .($key + 1). ' يجب أن يحتوي علي رقم يتكون من 1 الي 10 رقمًا/أرقام';

                    }


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
