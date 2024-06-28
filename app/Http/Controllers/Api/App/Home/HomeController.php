<?php

namespace App\Http\Controllers\Api\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Offer\OfferRequest;
use App\Http\Resources\Api\App\Address\AddressResource;
use App\Http\Resources\Api\App\Category\{CategoryResource};
use App\Http\Resources\Api\App\Home\{FlashSaleResource, MainCategoryResource, SimpleCategoryResource, SimpleFlashSaleResource, SimpleOfferResource, SimpleProductResource, SliderResource, SubCategoryResource};
use App\Http\Resources\Api\App\Product\SimpleProductDetailsResource;
use App\Http\Resources\Api\Website\Home\SliderItemResource;
use App\Models\{Address, Category, FlashSale, FlashSaleProduct, Offer, Product, ProductDetails, Slider};
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{


    public function index(Request $request)
    {
        $now = Carbon::now();
        $data = [];
        // check on user_max  when complete db tables (whereHas)
        // product has related have quantities
        // products in  this main category id
        $offers =  Offer::where('is_active', 1)
            ->where('remain_use', '>', 0)
            ->whereDate('start_at', '<=',  $now)
            ->whereDate('end_at', '>=',  $now)
            ->whereIn('display_platform', ['app', 'both'])
            ->orderBy('ordering', 'desc')->take(5)->get();
        $main_categories  = Category::where(['is_active' => true, 'parent_id' =>  null, 'position' => 'main'])->orderBy('ordering', 'asc')->take(5)->get();
        $main_category_id  = $request->main_category_id  != null ? $request->main_category_id : ($main_categories->count() > 0 ? $main_categories[0]['id'] : null);
        // put here  parent_id  == main_category_id
        $sub_categories =  Category::where(['is_active' => true, 'parent_id' =>  $main_category_id])->orderBy('ordering', 'asc')->take(5)->get();
        $recommended = Product::where(['is_active' => true, 'main_category_id' => $main_category_id])->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
        })->orderBy('ordering', 'asc')->take(5)->get();
        // $most_orders = Product::where(['is_active' => true, 'main_category_id' => $main_category_id])->whereHas('productDetails', function ($q) {
        //     $q->where('quantity', '>=', 0);
        // })->orderBy('ordering', 'asc')->take(5)->get();
        
        $most_orders = Product::where(['is_active' => true, 'main_category_id' => $main_category_id])->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
        })->join('product_details', 'products.id', '=', 'product_details.product_id')
            ->orderBy('product_details.sold', 'desc')
            ->groupBy('product_id')
            ->select('products.*')->distinct()->take(5)->get();

        // $top_rated = Product::where(['is_active' => true, 'main_category_id' => $main_category_id])->whereHas('productDetails', function ($q) {
        //     $q->where('quantity', '>', 0);
        // })->orderBy('ordering', 'asc')->take(5)->get();


        $top_rated = Product::where(['is_active' => true])->whereHas('productDetails', function ($q) {
            $q->where('quantity', '>=', 0);
        })
            ->join('product_details', 'products.id', '=', 'product_details.product_id')
            ->orderBy('product_details.rate_avg', 'desc')
            ->groupBy('product_id')
            ->select('products.*')->distinct()->take(5)->get();

        $slider = Slider::where(['is_active' => true, 'category_id' => $main_category_id])->whereIn('platform', ['app', 'all'])->orderBy('ordering', 'asc')->first();
        $flash_sales = FlashSale::where('is_active', true)
            ->whereDate('start_at', '<=',  $now)
            ->whereDate('end_at', '>=',  $now)
            ->whereHas('flashSaleProducts', function ($q) use ($main_category_id) {
                $q->where(\DB::raw('quantity - sold'), '>=', 0);
                // $q->whereHas('product', function ($q) use ($main_category_id) {
                //     $q->where(['is_active' => true, 'main_category_id' => $main_category_id]);
                // });
            })
            ->first();


        $data = [
            [
                'type' => 'offers',
                'text' => null,
                'data' =>  SimpleOfferResource::collection($offers),
            ],
            // [
            //     'type' => 'slider',
            //     'text' => null,
            //     'data' => $slider != null ?  new SliderResource(Slider::where(['is_active' => true, 'category_id' => $main_category_id])->whereIn('platform',['app','all'])->orderBy('ordering', 'asc')->first()) : null,
            // ],
            // [
            //     'type' => 'main_category',
            //     'text' => null,
            //     'data' =>  SimpleCategoryResource::collection($main_categories),
            // ],
            [
                'type' => 'sub_category',
                'text' => trans('app.messages.shop_by_category'),
                'data' =>  SubCategoryResource::collection($sub_categories),
            ],
            // [
            //     'type' => 'offers',
            //     'text' => null,
            //     'data' =>  SimpleOfferResource::collection($offers),
            // ],
            [
                'type' => 'products',
                'text' => trans('app.messages.recommended'),
                'data' =>  SimpleProductResource::collection($recommended),
            ],
            [
                'type' => 'products',
                'text' => trans('app.messages.most_orders'),
                'data' =>  SimpleProductResource::collection($most_orders),
            ],
            [
                'type' => 'products',
                'text' => trans('app.messages.top_rated'),
                'data' =>  SimpleProductResource::collection($top_rated),
            ],
            [
                'type' => 'flash_sale',
                'text' => trans('app.messages.flash_sale'),
                'data' =>  $flash_sales ?   SimpleFlashSaleResource::make($flash_sales) : null,
            ],
            [
                'type' => 'sliders',
                'text' => null,
                'data' =>  SliderResource::collection(Slider::where(['is_active' => true, 'category_id' => $main_category_id])->whereIn('platform', ['app', 'all'])->orderBy('ordering', 'asc')->take(3)->get()),
            ],
            

        ];
        $address  = null;
        $default_address = auth()->guard('api')->user() != null ? $address =  Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->first() : null;
        if ($address  != null) {
            $default_address = new AddressResource($address);
        }
        return response()->json(['data' => $data, 'status' => 'success', 'message' => '', 'default_address' =>  $default_address]);
    }

    public function slider_details($id) {

        $slider = Slider::findOrFail($id);

        $products = null;

        if($slider->item_type == 'products') {
            $products = Product::whereIn('id',json_decode($slider->item_id))->get();
        } 

        return response()->json(['data' => $products, 'status' => 'success', 'message' => '']);
        // return SliderItemResource::make($slider)->additional(['status' => 'success', 'message' => '']);
    }
    
    public function flashSale(Request $request)
    {
        // dd('pppppppppppaaaaaa');
        $now = Carbon::now();
        $categories = $request->main_category_id != null ?  thirdLavels(Category::find($request->main_category_id)) : null;
        $flash_salesProducts = ProductDetails::when($categories, function ($q) use ($categories) {
            $q->whereHas('product', function ($q) use ($categories) {
                $q->where('is_active', true);
                $q->whereHas('categoryProducts', function ($q) use ($categories) {
                    $q->whereIn('category_id', $categories->pluck('id')->toArray());
                });
            });
        })
            ->whereHas('flashSalesProduct', function ($q) use ($request) {
                $q->where(\DB::raw('quantity - sold'), '>=', 0)->whereHas('flashSale', function ($q) use ($request) {
                    $q->where('is_active', true);
                    $q->when($request->type == 'now', function ($query) {
                        $query->whereDate('start_at', '<=',  now());
                        $query->whereDate('end_at', '>=',  now());
                    });
                    $q->when($request->type == 'later', function ($query) {
                        $query->whereDate('start_at', '>',   now());
                    });
                });
            })

            ->paginate(6);
        $flash_sale = FlashSale::where('is_active', true)
            ->when($request->type == 'now', function ($query) use ($now) {
                $query->whereDate('start_at', '<',   $now);
                $query->whereDate('end_at', '>=',   $now);
            })->when($request->type == 'later', function ($query) use ($now) {
                $query->whereDate('start_at', '>',  $now);
                // $query->whereDate('end_at', '>=',  Carbon::tomorrow());
            })
            ->first();



        $additionData = null;
        if ($flash_sale) {
            $start_at = Carbon::parse($flash_sale->start_at);
            $end_at = Carbon::parse($flash_sale->end_at);
            $diff = $start_at->diff($end_at)->format('%h:%I:%s');


            $additionData = [
                'end_at' => $flash_sale->end_at,
                'start_at' => $flash_sale->start_at,
                'ends_in' => $diff,
                'type' => $request->type,
            ];
        }
        return response()->json(['data' =>   SimpleProductDetailsResource::collection($flash_salesProducts), 'status' => 'success', 'message' => '', 'additionData' => $additionData]);
    }

    public function getCategories(Request $request)
    {
        $main_categories  = Category::where(['is_active' => true, 'parent_id' =>  null, 'position' => 'main'])->orderBy('ordering', 'asc')->get();
        $main_category_id  = $request->main_category_id  != null ? $request->main_category_id : (isset($main_categories) ? $main_categories[0]['id'] : null);

        $sub_categories =  Category::where(['is_active' => true, 'parent_id' =>    $main_category_id, 'position' => 'first_sub'])->orderBy('ordering', 'asc')->get();

        $second_category = $request->second_category_id != null ? $request->second_category_id : (isset($sub_categories)   && $sub_categories->count()  > 0 ? $sub_categories[0]['id'] : null);
        $recommended = Product::where(['is_active' => true, 'main_category_id' => $main_category_id])->whereHas('categoryProducts', function ($q) use ($second_category) {
            $q->whereHas('category', function ($q) use ($second_category) {
                $q->where('id', $second_category);
            });
        })->orderBy('ordering', 'asc')->take(6)->get();
        $second_category_data =  Category::where(['id' => $second_category, 'is_active' => true])->first();
        $third_category =   $second_category_data  != null  ?  thirdLavels($second_category_data) :  null;
        $data = [
            [
                'type' => 'slider',
                'text' => null,
                'data' =>  SliderResource::collection(Slider::where(['is_active' => true, 'category_id' =>   $second_category])->whereIn('platform', ['app', 'all'])->orderBy('ordering', 'asc')->take(3)->get()),
            ],
            [
                'type' => 'main_category',
                'text' => null,
                'data' =>  SimpleCategoryResource::collection($main_categories),
            ],
            [
                'type' => 'sub_category',
                'text' => null,
                'data' =>  SimpleCategoryResource::collection($sub_categories),
            ],
            [
                'type' => 'third_category',
                'text' => null,
                'data' =>  $third_category ? SubCategoryResource::collection($third_category) : [],
            ],
            [
                'type' => 'recommended',
                'text' => trans('app.messages.recommended'),
                'data' =>  SimpleProductResource::collection($recommended),
            ],

        ];
        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }

    public function getCategory(Request $request)
    {
        $categories = Category::where('is_active', 1)->when($request->category_id == null, function ($query) {
            $query->where('position', 'main');
        })->when($request->category_id, function ($query) use ($request) {
            $query->where('parent_id', $request->category_id);
        })->orderBy('ordering', 'asc')->get();
        $mainCategory = $request->category_id ? root(Category::find($request->category_id)) : null;

        return (SimpleCategoryResource::collection($categories))->additional(['status' => 'success', 'message' => '', 'slider' => $request->category_id != null ? SliderResource::collection($mainCategory->sliders()->whereIn('platform', ['app', 'all'])->inRandomOrder()->take(2)->get()) : []]);
    }
    public function getcategoryData(Request $request)
    {
        $categories = Category::where(['is_active' => 1, 'parent_id' => $request->category_id])->orderBy('ordering', 'asc')->get();
        $recommended = Product::where(['is_active' => true])->whereHas('categoryProducts', function ($q) use ($categories) {
            $q->whereHas('category', function ($q) use ($categories) {
                $q->whereIn('id', $categories->pluck('id')->toArray());
            });
        })->orderBy('ordering', 'asc')->take(6)->get();
        $data = [
            [
                'type' => 'slider',
                'text' => null,
                'data' =>  SliderResource::collection(Slider::where(['is_active' => true, 'category_id' =>   $request->category_id])->whereIn('platform', ['app', 'all'])->orderBy('ordering', 'asc')->take(1)->get()),
            ],
            [
                'type' => 'category',
                'text' => null,
                'data' => SubCategoryResource::collection($categories),
            ],
            [
                'type' => 'products',
                'text' => trans('app.messages.recommended'),
                'data' =>  SimpleProductResource::collection($recommended),
            ],
            [
                'type' => 'slider',
                'text' => null,
                'data' =>  SliderResource::collection(Slider::where(['is_active' => true, 'category_id' =>   $request->category_id])->whereIn('platform', ['app', 'all'])->orderBy('ordering', 'asc')->take(1)->get()),
            ],
        ];
        return response()->json(['data' => $data, 'status' => 'success', 'message' => '']);
    }
    public function categoryLayers()
    {
        $categories = Category::where(['is_active' => true, 'position' => 'main', 'parent_id' => null])->get();
        return (CategoryResource::collection($categories))->additional(['status' => 'success', 'message' => '']);
    }

    private function dataOfBuyXGetY($offer, $type)
    {
        $offerProducts  = [];
        if ($type == 'buy_x') {
            if ($offer->buyToGetOffer->buy_apply_on == 'special_products') {
                $offerProducts =  ProductDetails::when($offer, function ($q) use ($offer) {
                    $q->whereIn('id', $offer->buyToGetOffer->buy_apply_ids);
                    $q->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                    });
                })->paginate(6);
            } elseif ($offer->buyToGetOffer->buy_apply_on == 'special_categories') {
                $offerProducts =  ProductDetails::whereHas('product', function ($q) use ($offer) {
                    $q->where('is_active', true);
                    $q->whereHas('categoryProducts', function ($q) use ($offer) {
                        $q->whereIn('category_id', $offer->buyToGetOffer->buy_apply_ids);
                    });
                })->paginate(6);
            } else {
            }
        } elseif ($type == 'get_y') {
            if ($offer->buyToGetOffer->get_apply_on == 'special_products') {
                $offerProducts =  ProductDetails::when($offer, function ($q) use ($offer) {
                    $q->whereIn('id', $offer->buyToGetOffer->get_apply_ids);
                    $q->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                    });
                })->paginate(6);
            } elseif ($offer->buyToGetOffer->get_apply_on == 'special_categories') {
                $offerProducts =  ProductDetails::whereHas('product', function ($q) use ($offer) {
                    $q->where('is_active', true);
                    $q->whereHas('categoryProducts', function ($q) use ($offer) {
                        $q->whereIn('category_id', $offer->buyToGetOffer->get_apply_ids);
                    });
                })->paginate(6);
            }
        }
        return  $offerProducts;
    }

    public function offerProducts(OfferRequest $request,  $offer_id)
    {
        $additionData  = [];
        $offer = Offer::findOrFail($offer_id);

        if ($offer->type == 'buy_x_get_y') {

            $offerProducts = $this->dataOfBuyXGetY($offer, $request->type);
        } elseif ($offer->type == 'fix_amount'  || $offer->type == 'percentage') {
            if ($offer->discountOfOffer->apply_on  == 'special_products') {
                $offerProducts =  ProductDetails::when($offer, function ($q) use ($offer) {
                    $q->whereIn('id', $offer->discountOfOffer->apply_ids);
                        $q->groupBy('product_id');
                    $q->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                    });
                })->paginate(6);
            } elseif ($offer->discountOfOffer->apply_on == 'special_categories') {
                $offerProducts =  ProductDetails::whereHas('product', function ($q) use ($offer) {
                    $q->where('is_active', true);
                    $q->whereHas('categoryProducts', function ($q) use ($offer) {
                        $q->whereIn('category_id', $offer->discountOfOffer->apply_ids);
                    });
                })->paginate(6);
            } else {
            }
        }
        $start_at = Carbon::parse($offer->start_at);
        $end_at = Carbon::parse($offer->end_at);
        $diff = $start_at->diff($end_at)->format('%h:%I:%s');
        $additionData += [
            'end_at' => $offer->end_at,
            'start_at' => $offer->start_at,
            'ends_in' => $diff,
        ];
        return (SimpleProductDetailsResource::collection($offerProducts))->additional(['status' => 'success', 'message' => '', 'additionData' => $additionData]);
    }
    
}
