<?php

namespace App\Http\Controllers\Api\Dashboard\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Dashboard\Notification\NotificationResource;
use App\Http\Resources\Api\Dashboard\Order\SimpleOrderResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductDetailResource;
use App\Http\Resources\Api\Dashboard\Rate\RateResource;
use App\Http\Resources\Api\Dashboard\Wallet\WithdrawalResource;
use App\Models\Order;
use App\Models\OrderPriceDetail;
use App\Models\OrderProduct;
use App\Models\OrderRate;
use App\Models\{Cart, Category, FlashSale, Offer, Product,Slider};
use App\Models\ProductDetails;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Http\Resources\Api\Dashboard\Client\SimpleClientResource;
use App\Http\Resources\Api\Dashboard\Product\SimpleProductDetailResource4;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{


    public function home_v2()
    {
        $data = [];

        return response()->json(['status' => 'success', 'data' => $data, 'messages' => '']);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function countOrderPrice($year, $month)
    {
        $orders = OrderPriceDetail::whereHas('order', function ($q) use ($year, $month) {
            $q->where(['status' => 'admin_delivered', 'is_payment' => 'paid'])->whereYear('updated_at', $year)
                ->whereMonth('updated_at', $month);
        })->sum('total_price');

        return (int)$orders;
    }


    private function countOrderMonthPrice($day)
    {
        $orders = OrderPriceDetail::whereHas('order', function ($q) use ($day) {
            $q->where(['status' => 'admin_delivered', 'is_payment' => 'paid'])->whereDay('updated_at', $day);
        })->sum('total_price');

        return (int)$orders;
    }


    public function index(Request $request)
    {
        $admins = User::whereIn('user_type', ['admin', 'superadmin'])->pluck('id')->toArray();

        $min_stock = setting('minimum_stock') == null ? 5 : setting('minimum_stock');

        $notifications = DatabaseNotification::whereHasMorph('notifiable', [User::class], function ($q) use ($admins) {
            $q->whereIn('notifiable_id', $admins);
        })->latest()->take(20)->get();

        $orders                        = Order::all();

        $sliders                       = Slider::where('is_active', 1);
        $main_categories               = \App\Models\Category::whereNull('parent_id')->where('position','main');
        $sub_categories                = \App\Models\Category::where('parent_id','!=',null)->where('position','!=','main');
        $products                      = \App\Models\Product::Has('productDetails')->get();
        $soled_products                = \App\Models\Product::whereHas('productDetails',function($q)use($min_stock){
                                                                                $q->where('quantity' ,'<=',$min_stock);
                                                                        });
        $clients                       =  User::where('user_type','client');

        $currentMonth = Carbon::now()->month;
        $month_orders = Order::whereMonth('created_at', $currentMonth)->where('status', 'admin_delivered')
                   ->get();
        $current_month_incom =   (int)OrderPriceDetail::whereIn('order_id', $month_orders->pluck('id')->toArray())->sum('total_price');
        // dd(OrderPriceDetail::whereIn('order_id', $month_orders->pluck('id')->toArray())->sum('total_price'));


        $previousMonth = Carbon::now()->subMonth()->month;
        $previous_month_orders = Order::whereMonth('created_at', $previousMonth)->where('status', 'admin_delivered')
                   ->get();
        $previous_month_incom =   (int)OrderPriceDetail::whereIn('order_id', $previous_month_orders->pluck('id')->toArray())->sum('total_price');

         $client_top= User::whereHas('orders')->withCount(['orders' => function($q) {
                $q->where('status', 'admin_delivered');
            }])->orderBy('orders_count', 'desc')->latest()->first();

        // dd($client_top->id);
        $best_seller = $client_top ? new SimpleClientResource($client_top) : null;



        $orders_completed = Order::where('status', 'admin_delivered');

        $cards['total_orders']     = ['url' => 'orders/show-all','value' => $orders->count()];
        $cards['clients']          = ['url' => 'users/show-all','value' => $clients->count()];
        $cards['products']          = ['url' => 'products/show-all','value' => $products->count()];
        $cards['total_revenue']    = ['url' => '/','value' => OrderPriceDetail::whereIn('order_id', $orders_completed->pluck('id')->toArray())->sum('total_price')];
        // $cards['total_prdocuts']   = ['url' => 'products/show-all','value' => Product::count()];



        // by karim reports
        // $cards['sliders']     = ['url' => 'slider/show-all','value' => $orders->count()];

        $data['recent_orders']     = SimpleOrderResource::collection(Order::latest()->take(5)->get());
        $data['top_product']       = SimpleProductDetailResource::collection(ProductDetails::orderBy('sold', 'desc')->take(5)->get());
        $data['recent_reviews']    = RateResource::collection(OrderRate::latest()->take(5)->get());
        $data['notifications']     = NotificationResource::collection($notifications);
        $data['transfer_requests'] = WithdrawalResource::collection(Withdrawal::latest()->take(5)->get());
        $data['products_out_of_stock'] = SimpleProductDetailResource::collection(ProductDetails::where('quantity', '<=', setting('minimum_stock') ?? 0)->take(5)->get());



        $i = 0;
        foreach ($cards as $key => $arr) {
            $data['upper_charts'][] = [
                'id'    => $i += 1,
                'key'   => $key,
                'name'  => trans('dashboard.statistics.' . $key),
                'count' => $arr['value'],
                'route' => $arr['url']
            ];
        }

        $products = OrderProduct::whereHas('order', function ($query) {
            $query->where('status', 'admin_delivered');
        })->whereYear('created_at', now())->groupBy('product_detail_id')->orderByRaw('COUNT(*) DESC')->take(4)->get();

        $sellingProduct = [];

        if ($products) {
            foreach ($products as $product) {
                $productCount = [];
                foreach (['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'] as $month) {
                    $count = OrderProduct::whereHas('order', function ($query) {
                        $query->where('status', 'admin_delivered');
                    })->whereYear('created_at', now()->subYear())->where('product_detail_id', $product->product_detail_id)->whereMonth('updated_at', $month)->count();

                    array_push($productCount, $count);
                }

                array_push($sellingProduct, [
                    'name' => @$product->productDetail->product->name,
                    'type' => 'column',
                    'data' => $productCount,
                ]);
            }
        }

        $top_selling_product = [
            'title'  => trans('dashboard.statistics.top_selling_product'),
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'series' => $sellingProduct
        ];

        $months = $request->filter == 'last_3' ? 3 : ($request->filter == 'last_6' ? 6 : 12);

        $last_months = collect(today()->startOfMonth()->subMonths($months - 1)->monthsUntil(today()->startOfMonth()))->map(function ($date) {
            return ['year'=> $date->year, 'month' => $date->month, 'month_name' => $date->monthName];
        });

        $firstData = [];
        $lastData  = [];

        $monthData  = [];


        foreach ($last_months as $date) {
            array_push($firstData, $this->countOrderPrice($date['year'], $date['month']));
            array_push($lastData, $this->countOrderPrice($date['year'] - 1, $date['month']));
        }

        $revenue_chart = [
            'title'  => trans('dashboard.statistics.revenue_chart'),
            'labels' => $last_months,
            'series' => [
                [
                    'name' => trans('dashboard.statistics.revenue_chart.this_year'),
                    'type' => 'line',
                    'data' => $firstData,
                ],
                [
                    'name' => trans('dashboard.statistics.revenue_chart.last_year'),
                    'type' => 'line',
                    'data' => $lastData
                ],
            ]
        ];

        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to   = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Get the current date
        $now = Carbon::now();

        // Get the first day of the current month
        $startOfMonth = $now->copy()->startOfMonth();

        // Get the last day of the current month
        $endOfMonth = $now->copy()->endOfMonth();

        // Initialize an empty collection to store the days
        $days = collect();

        // Loop through the month from start to end
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {

            // Add the current date to the collection
            $days->push($date->copy());

            $current_month_days_names[] = [
                'month' => Carbon::now()->format('m'),
                'day' => Carbon::parse($date->copy())->format('Y-m-d'),
                'day_name' => Carbon::parse($date->copy())->format('l')
            ];
        }

        foreach ($days as $day) {
            array_push($monthData, $this->countOrderMonthPrice($day));
        }


        $revenue_month_chart = [
            'title'  => trans('dashboard.statistics.revenue_this_month_chart'),
            'labels' => $current_month_days_names,
            'series' => [
                [
                    'name' => trans('dashboard.statistics.revenue_this_month_chart.this_month'),
                    'type' => 'line',
                    'data' => $monthData,
                ]
            ]
        ];

        $orders            =  Order::whereIn('status', ['pending', 'admin_accept', 'admin_rejected', 'admin_shipping', 'admin_delivered'])->count();
        $pendingOrders     = $orders > 0 ? (Order::where('status', 'pending')->count() / $orders) * 100 : 0;
        $adminAcceptOrders = $orders > 0 ?  (Order::where('status', 'admin_accept')->count() / $orders) * 100 : 0;
        $adminRejectOrders = $orders > 0 ?  (Order::where('status', 'admin_rejected')->count() / $orders) * 100 : 0;
        $deliveredOrders   = $orders > 0 ?  (Order::where('status', 'admin_delivered')->count() / $orders) * 100 : 0;

        $order_status = [
            'title'  => trans('dashboard.statistics.order_status'),
            'labels' => [trans('app.messages.status.labels.pending'), trans('app.messages.status.labels.admin_accept'),trans('app.messages.status.labels.admin_rejected'), trans('app.messages.status.labels.admin_delivered')],
            'series' => [
                [
                    'name' => trans('dashboard.statistics.order_status.pending'),
                    'type' => 'donut',
                    'data' => $pendingOrders,
                ],
                [
                    'name' => trans('dashboard.statistics.order_status.admin_accepted'),
                    'type' => 'donut',
                    'data' => $adminAcceptOrders,
                ],
                [
                    'name' => trans('dashboard.statistics.order_status.admin_rejected'),
                    'type' => 'donut',
                    'data' => $adminRejectOrders,
                ],
                [
                    'name' => trans('dashboard.statistics.order_status.admin_delivered'),
                    'type' => 'donut',
                    'data' => $deliveredOrders,
                ]
            ]
        ];


        $bestSellingProductsArr  = OrderProduct::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->pluck('product_id')->toArray();

        if ($bestSellingProductsArr ) {
            $most_sale_product = Product::whereIn('id',$bestSellingProductsArr)->take(1)->get();
            $most_sale_products = Product::whereIn('id',$bestSellingProductsArr)->take(5)->get();
        } else {
            $most_sale_product = null;
            $most_sale_products = null;
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'recent_orders'             => $data['recent_orders'],
                'top_product'               => $data['top_product'],
                'recent_reviews'            => $data['recent_reviews'],
                'notifications'             => $data['notifications'],
                'products_out_of_stock'     => $data['products_out_of_stock'],
                'transfer_requests'         => $data['transfer_requests'],
                'upper_chart'               => $data['upper_charts'],
                'revenue_chart'             => $revenue_chart,
                'revenue_month_chart'       => $revenue_month_chart,
                'order_status'              => $order_status,
                'top_selling_product_chart' => $top_selling_product,
                'best_seller' => $best_seller,
                'current_month_incom'       => $current_month_incom ,
                'previous_month_incom'      => $previous_month_incom ,
                'diff_incom'                => $current_month_incom - $previous_month_incom ,

                'monthsstatistics' => [
                    'total_orders' => [
                        'id' => 1,
                        'key' => 'total_orders',
                        'count' => Order::whereBetween('created_at', [$from , $to])->count(),
                    ],
                    'pending_orders' => [
                        'id' => 2,
                        'key' => 'pending_orders',
                        'count' => Order::whereBetween('created_at', [$from , $to])->where('status', 'pending')->count(),
                    ],
                    'completed_orders' => [
                        'id' => 3,
                        'key' => 'completed_orders',
                        'count' => Order::whereBetween('created_at', [$from , $to])->where('status', 'admin_shipping')->count(),
                    ],
                    'canceled_orders' => [
                        'id' => 4,
                        'key' => 'canceled_orders',
                        'count' => Order::whereBetween('created_at', [$from , $to])->where('status', 'client_cancel')->count(),
                    ],
                    'total_revenue' => [
                        'id' => 5,
                        'key' => 'total_revenue',
                        'count' => OrderPriceDetail::whereHas('order',function($order) use($from , $to) {
                            $order->whereBetween('created_at', [$from , $to])->where('is_payment', 'paid');
                        })->sum('total_price'),
                    ],
                    'cart' => [
                        'id' => 6,
                        'key' => 'cart',
                        'count' => Cart::whereBetween('created_at', [$from , $to])->count(),
                    ],
                    'withdrawal' => [
                        'id' => 7,
                        'key' => 'withdrawal',
                        'count' => Withdrawal::whereBetween('created_at', [$from , $to])->count(),
                    ],
                    'total_offers' => [
                        'id' => 8,
                        'key' => 'total_offers',
                        'count' => Offer::whereBetween('start_at', [$from , $to])->count(),
                    ],
                    'total_flash_sale' => [
                        'id' => 9,
                        'key' => 'total_flash_sale',
                        'count' => FlashSale::whereBetween('start_at', [$from , $to])->count(),
                    ],
                ],


                'generalSummary' => [
                    'total_products' => [
                        'id' => 1,
                        'key' => 'total_products',
                        'count' => Product::count(),
                    ],
                    'clients' => [
                        'id' => 2,
                        'key' => 'clients',
                        'count' => User::where('user_type','client')->count(),
                    ],
                    'categories' => [
                        'id' => 3,
                        'key' => 'categories',
                        'count' => Category::count(),
                    ],
                    'total_orders' => [
                        'id' => 4,
                        'key' => 'total_orders',
                        'count' => Order::count(),
                    ],
                    'rates' => [
                        'id' => 5,
                        'key' => 'rates',
                        'count' => OrderRate::count(),
                    ],
                    'product_stocks' => [
                        'id' => 6,
                        'key' => 'product_stocks',
                        'count' => ProductDetails::whereHas('product')->where('quantity', '<=', setting('minimum_stock') ?? 0)->count()
                    ],
                ],


                'most_sale_product' => $most_sale_product != null ? SimpleProductDetailResource4::collection($most_sale_product) : [],
                'most_sale_products' => $most_sale_products != null && $most_sale_products->count() > 0 ? SimpleProductDetailResource4::collection($most_sale_products) : [],

            ],
            'messages' => ""
        ]);
    }
}
