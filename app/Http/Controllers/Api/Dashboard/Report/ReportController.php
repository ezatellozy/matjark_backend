<?php

namespace App\Http\Controllers\Api\Dashboard\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Report\ProductsRequest;
use App\Http\Requests\Api\Dashboard\Report\SalesRequest;
use App\Http\Resources\Api\Dashboard\Report\MostSaleProductsReportResource;
use App\Http\Resources\Api\Dashboard\Report\ReminderProductsReportResource;
use App\Http\Resources\Api\Dashboard\Report\SalesCategoriesReportResource;
use App\Http\Resources\Api\Dashboard\Report\SalesCitiesReportResource;
use App\Http\Resources\Api\Dashboard\Report\SalesProductsReportResource;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\City;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderRate;
use App\Models\Product;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class ReportController extends Controller
{


    public function sales(SalesRequest $request) {

        if($request->sub_report_type == 'product_sales') {

            $rows = OrderProduct::groupBy('product_id')->whereHas('order',function($order) {

                $order->where('is_payment','paid');

            })->when($request->from && $request->to, function ($query) use ($request) {

                $query->whereBetween('created_at', [$request->from, $request->to]);

            })->when($request->from, function ($query) use ($request) {

                $query->whereDate('created_at','>=',$request->from);

            })->when($request->to, function ($query) use ($request) {

                $query->whereDate('created_at','<=',$request->to);

            })->paginate();

            return response()->json(['status' => 'success', 'data' => SalesProductsReportResource::collection($rows)->response()->getData(true), 'messages' => '']);

        } elseif($request->sub_report_type == 'category_sales') {

            $productsArr = OrderProduct::groupBy('product_id')->whereHas('order',function($order) {

                $order->where('is_payment','paid');

            })->when($request->from && $request->to, function ($query) use ($request) {

                $query->whereBetween('created_at', [$request->from, $request->to]);

            })->when($request->from, function ($query) use ($request) {

                $query->whereDate('created_at','>=',$request->from);

            })->when($request->to, function ($query) use ($request) {

                $query->whereDate('created_at','<=',$request->to);

            })->pluck('product_id')->toArray();

            $categoriesArr = CategoryProduct::whereIn('product_id',$productsArr)->pluck('category_id')->toArray();

            $categories = Category::whereIn('id',$categoriesArr)->paginate(10);

            return response()->json(['status' => 'success', 'data' => SalesCategoriesReportResource::collection($categories)->response()->getData(true), 'messages' => '']);

        } elseif($request->sub_report_type == 'city_sales') {

            $cities = City::whereHas('addresses',function($addresses) use($request) {

                $addresses->whereHas('orders',function($orders) use($request) {

                    $orders->where('status', 'admin_delivered')

                    ->when($request->from && $request->to, function ($query) use ($request) {

                        $query->whereBetween('created_at', [$request->from, $request->to]);

                    })->when($request->from, function ($query) use ($request) {

                        $query->whereDate('created_at','>=',$request->from);

                    })->when($request->to, function ($query) use ($request) {

                        $query->whereDate('created_at','<=',$request->to);
                    });
                });

            })->paginate(10);

            return response()->json(['status' => 'success', 'data' => SalesCitiesReportResource::collection($cities)->response()->getData(true), 'messages' => '']);
        }

    }


    public function products(ProductsRequest $request) {

        if($request->sub_report_type == 'most_sales_products') {

            $productsArr = OrderProduct::groupBy('product_id')->whereHas('order',function($order) {

                $order->where('is_payment','paid');

            })->when($request->from && $request->to, function ($query) use ($request) {

                $query->whereBetween('created_at', [$request->from, $request->to]);

            })->when($request->from, function ($query) use ($request) {

                $query->whereDate('created_at','>=',$request->from);

            })->when($request->to, function ($query) use ($request) {

                $query->whereDate('created_at','<=',$request->to);

            })->pluck('product_id')->toArray();

            $products = Product::select('products.*')
                ->selectRaw('SUM(order_products.quantity) as total_quantity')
                ->havingRaw('total_quantity > ?', [0])
                // ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->leftJoin('order_products', function($join) use($productsArr) {
                    $join->on('products.id', '=', 'order_products.product_id')->whereIn('order_products.product_id', $productsArr);
                })
                ->groupBy('products.id')
                ->orderByDesc('total_quantity')
                ->take(10)
                ->get();

            return response()->json(['status' => 'success', 'data' => MostSaleProductsReportResource::collection($products)->response()->getData(true), 'messages' => '']);

        } elseif($request->sub_report_type == 'reminder_products') {

            $reminder = Reminder::when($request->from && $request->to, function ($query) use ($request) {

                $query->whereBetween('created_at', [$request->from, $request->to]);

            })->when($request->from, function ($query) use ($request) {

                $query->whereDate('created_at','>=',$request->from);

            })->when($request->to, function ($query) use ($request) {

                $query->whereDate('created_at','<=',$request->to);

            })->paginate();

            return response()->json(['status' => 'success', 'data' => ReminderProductsReportResource::collection($reminder)->response()->getData(true), 'messages' => '']);

        }
    }


    public function clients(Request $request) {

        $usersArr = User::where('user_type','client')->pluck('id')->toArray();

        $userHaveOrdersArr = Order::when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })->when($request->from, function ($query) use ($request) {
            $query->whereDate('created_at','>=',$request->from);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('created_at','<=',$request->to);
        })->orderBy('user_id','asc')->pluck('user_id')->toArray();

        $userHaveOrdersArr = array_unique($userHaveOrdersArr);

        $userNotHaveOrdersArr = array_merge(array_diff($usersArr, $userHaveOrdersArr), array_diff($userHaveOrdersArr, $usersArr));
        $userNotHaveOrdersArr = array_unique($userNotHaveOrdersArr);

        $data['users_have_orders_count'] = count($userHaveOrdersArr);
        $data['users_not_have_orders_count'] = count($userNotHaveOrdersArr);

        $data['ratio_of_users_sales'] = round((count($userHaveOrdersArr) / count($usersArr)) * 100);

        $avg = round(OrderRate::avg('rate'), 2);

        $rate_avg = number_format($avg,2);

        $data['rate_avg'] = $rate_avg;
        $data['rate_avg_ratio'] = number_format( ($avg / 5) * 100 ,2);

        $stars_5_count = OrderRate::when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })->when($request->from, function ($query) use ($request) {
            $query->whereDate('created_at','>=',$request->from);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('created_at','<=',$request->to);
        })->where(['status' =>'accepted', 'rate' => 5])->count();


        $stars_4_count = OrderRate::when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })->when($request->from, function ($query) use ($request) {
            $query->whereDate('created_at','>=',$request->from);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('created_at','<=',$request->to);
        })->where(['status' =>'accepted'])->where('rate', '<', 5)->where('rate', '>=', 4)->count();


        $stars_3_count = OrderRate::when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })->when($request->from, function ($query) use ($request) {
            $query->whereDate('created_at','>=',$request->from);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('created_at','<=',$request->to);
        })->where(['status' =>'accepted'])->where('rate', '<', 4)->where('rate', '>=', 3)->count();


        $stars_2_count = OrderRate::when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })->when($request->from, function ($query) use ($request) {
            $query->whereDate('created_at','>=',$request->from);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('created_at','<=',$request->to);
        })->where(['status' =>'accepted'])->where('rate', '<', 3)->where('rate', '>=', 2)->count();


        $stars_1_count = OrderRate::when($request->from && $request->to, function ($query) use ($request) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        })->when($request->from, function ($query) use ($request) {
            $query->whereDate('created_at','>=',$request->from);
        })->when($request->to, function ($query) use ($request) {
            $query->whereDate('created_at','<=',$request->to);
        })->where(['status' =>'accepted'])->where('rate', '<', 2)->where('rate', '>=', 1)->count();

        $rates_count =  OrderRate::where(['status' =>'accepted'])->count();

        // $data['StarRateCount'] = [

        //     'stars_5_count' => $stars_5_count,
        //     'stars_5_average' => $rates_count > 0 ? number_format(($stars_5_count / $rates_count) * 100,2) : 0,

        //     'stars_4_count' => $stars_4_count,
        //     'stars_4_average' => $rates_count > 0 ? number_format(($stars_4_count / $rates_count) * 100,2) : 0,

        //     'stars_3_count' => $stars_3_count,
        //     'stars_3_average' => $rates_count > 0 ? number_format(($stars_3_count / $rates_count) * 100,2) : 0,

        //     'stars_2_count' => $stars_2_count,
        //     'stars_2_average' => $rates_count > 0 ? number_format(($stars_2_count / $rates_count) * 100,2) : 0,

        //     'stars_1_count' => $stars_1_count,
        //     'stars_1_average' => $rates_count > 0 ? number_format(($stars_1_count / $rates_count) * 100,2) : 0,

        // ];

        $data['StarRateCount'] = [

            'data' => [
                (float) $stars_1_count ,
                (float) $stars_2_count ,
                (float) $stars_3_count ,
                (float) $stars_4_count ,
                (float) $stars_5_count
            ],
        ];

        $data['StarRateAverage'] = [

            'data' => [
                $rates_count > 0 ? (float) number_format(($stars_1_count / $rates_count) * 100,2) : 0,
                $rates_count > 0 ? (float) number_format(($stars_2_count / $rates_count) * 100,2) : 0 ,
                $rates_count > 0 ? (float) number_format(($stars_3_count / $rates_count) * 100,2) : 0 ,
                $rates_count > 0 ? (float) number_format(($stars_4_count / $rates_count) * 100,2) : 0 ,
                $rates_count > 0 ? (float) number_format(($stars_5_count / $rates_count) * 100,2) : 0 ,
            ],
        ];

        return response()->json(['status' => 'success', 'data' => $data, 'messages' => '']);
    }


    public function most_orders_dates(Request $request) {


        $orderDates = Order::where('is_payment','paid')->

        when($request->status, function ($query) use($request) {
            $query->where('status', $request->status);
        })->

        when($request->from && $request->to, function ($query) use ($request) {

            $query->whereBetween('created_at', [$request->from, $request->to]);

        })->when($request->from, function ($query) use ($request) {

            $query->whereDate('created_at','>=',$request->from);

        })->when($request->to, function ($query) use ($request) {

            $query->whereDate('created_at','<=',$request->to);

        })->where('created_at','!=',null)->orderBy('created_at','asc')->pluck('created_at')->toArray();

        $sunday_count = 0;
        $monday_count = 0;
        $tuesday_count = 0;
        $wednesday_count = 0;
        $thursday_count = 0;
        $friday_count = 0;
        $saturday_count = 0;

        if(! empty($orderDates)) {

            foreach($orderDates as $date) {

                if(strtolower($date->format('l')) == 'sunday') {
                    $sunday_count = $sunday_count + 1;
                } elseif(strtolower($date->format('l')) == 'monday') {
                    $monday_count = $monday_count + 1;
                } elseif(strtolower($date->format('l')) == 'tuesday') {
                    $tuesday_count = $tuesday_count + 1;
                } elseif(strtolower($date->format('l')) == 'wednesday') {
                    $wednesday_count = $wednesday_count + 1;
                } elseif(strtolower($date->format('l')) == 'thursday') {
                    $thursday_count = $thursday_count + 1;
                } elseif(strtolower($date->format('l')) == 'friday') {
                    $friday_count = $friday_count + 1;
                } elseif(strtolower($date->format('l')) == 'saturday') {
                    $saturday_count = $saturday_count + 1;
                }
            }
        }



        // $data = [

        //     'sunday' => $sunday_count,

        //     'monday' => $monday_count,

        //     'tuesday' => $tuesday_count,

        //     'wednesday' => $wednesday_count,

        //     'thursday' => $thursday_count,

        //     'friday' => $friday_count,

        //     'saturday' => $saturday_count,

        // ];

        $data['days'] = [

            'data' => [
                $sunday_count,
                $monday_count,
                $tuesday_count,
                $wednesday_count,
                $thursday_count,
                $friday_count,
                $saturday_count,
            ],
        ];

        // return response()->json(['status' => 'success',  'data' => [
        //         $sunday_count,
        //         $monday_count,
        //         $tuesday_count,
        //         $wednesday_count,
        //         $thursday_count,
        //         $friday_count,
        //         $saturday_count,
        //     ], 'messages' => '']);

        return response()->json(['status' => 'success',  'data' => $data, 'messages' => '']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
