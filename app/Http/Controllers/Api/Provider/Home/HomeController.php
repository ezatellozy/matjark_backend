<?php

namespace App\Http\Controllers\Api\Provider\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Provider\Notification\NotificationResource;
use App\Http\Resources\Api\Provider\Order\SimpleOrderResource;
use App\Http\Resources\Api\Provider\Product\SimpleProductDetailResource;
use App\Http\Resources\Api\Provider\Rate\RateResource;
use App\Http\Resources\Api\Provider\Wallet\WithdrawalResource;
use App\Models\Order;
use App\Models\OrderPriceDetail;
use App\Models\OrderProduct;
use App\Models\OrderRate;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class HomeController extends Controller
{
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

        return $orders;
    }

    public function index(Request $request)
    {
        
        $admins = User::whereIn('user_type', ['provider'])->pluck('id')->toArray();
        $user = auth()->guard('api')->user();

        $notifications = DatabaseNotification::whereHasMorph('notifiable', [User::class], function ($q) use ($admins) {
            $q->whereIn('notifiable_id', $admins);
        })->latest()->take(20)->get();

        $orders                    = Order::where('status', 'admin_delivered');

        $cards['total_orders']     = $orders->count();
        $cards['total_revenue']    = OrderPriceDetail::whereIn('order_id', $orders->pluck('id')->toArray())->sum('total_price');
        $cards['total_prdocuts']   = Product::where('added_by_id',$user->id)->count();

        $data['recent_orders']     = SimpleOrderResource::collection(Order::latest()->take(5)->get());

        $top_product = ProductDetails::whereHas('product', function ($query) use ($user) {
            $query->where('added_by_id', $user->id);
        })->orderBy('sold', 'desc')->take(5)->get();

        $data['top_product']       = SimpleProductDetailResource::collection($top_product);
        $data['recent_reviews']    = RateResource::collection(OrderRate::latest()->take(5)->get());
        $data['notifications']     = NotificationResource::collection($notifications);
        $data['transfer_requests'] = WithdrawalResource::collection(Withdrawal::latest()->take(5)->get());
        $data['products_out_of_stock'] = SimpleProductDetailResource::collection(ProductDetails::where('quantity', '<=', setting('minimum_stock') ?? 0)->take(5)->get());

        $i = 0;
        foreach ($cards as $key => $value) {
            $data['upper_charts'][] = [
                'id'    => $i += 1,
                'key'   => $key,
                'name'  => trans('provider.statistics.' . $key),
                'count' => $value,
                'route' => '/' . $key
            ];
        }

        $products = OrderProduct::whereHas('order', function ($query) {
            $query->where('status', 'admin_delivered');
        })->whereHas('product', function ($query) use ($user) {
            $query->where('added_by_id', $user->id);
        })->whereYear('created_at', now())->groupBy('product_detail_id')->orderByRaw('COUNT(*) DESC')->take(4)->get();

        $sellingProduct = [];

        if ($products) {
            foreach ($products as $product) {
                $productCount = [];
                foreach (['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'] as $month) {
                    $count = OrderProduct::whereHas('order', function ($query) {
                        $query->where('status', 'admin_delivered');
                    })->whereHas('product', function ($query) use ($user) {
                        $query->where('added_by_id', $user->id);
                    })->whereYear('created_at', now()->subYear())->where('product_detail_id', $product->product_detail_id)->whereMonth('updated_at', $month)->count();

                    array_push($productCount, $count);
                }

                array_push($sellingProduct, [
                    'name' => $product->productDetail->product->name,
                    'type' => 'column',
                    'data' => $productCount,
                ]);
            }
        }

        $top_selling_product = [
            'title'  => trans('provider.statistics.top_selling_product'),
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'series' => $sellingProduct
        ];

        $months = $request->filter == 'last_3' ? 3 : ($request->filter == 'last_6' ? 6 : 12);

        $last_months = collect(today()->startOfMonth()->subMonths($months - 1)->monthsUntil(today()->startOfMonth()))->map(function ($date) { 
            return ['year'=> $date->year, 'month' => $date->month, 'month_name' => $date->monthName];
        });

        $firstData = [];
        $lastData  = [];

        foreach ($last_months as $date) {
            array_push($firstData, $this->countOrderPrice($date['year'], $date['month']));
            array_push($lastData, $this->countOrderPrice($date['year'] - 1, $date['month']));
        }

        $revenue_chart = [
            'title'  => trans('provider.statistics.revenue_chart'),
            'labels' => $last_months,
            'series' => [
                [
                    'name' => trans('provider.statistics.revenue_chart.this_year'),
                    'type' => 'line',
                    'data' => $firstData,
                ],
                [
                    'name' => trans('provider.statistics.revenue_chart.last_year'),
                    'type' => 'line',
                    'data' => $lastData
                ],
            ]
        ];

        $orders            =  Order::whereIn('status', ['pending', 'admin_accept', 'admin_rejected', 'admin_shipping', 'admin_delivered'])->count();
        $pendingOrders     = $orders > 0 ? (Order::where('status', 'pending')->count() / $orders) * 100 : 0;
        $adminAcceptOrders = $orders > 0 ?  (Order::where('status', 'admin_accept')->count() / $orders) * 100 : 0;
        $adminRejectOrders = $orders > 0 ?  (Order::where('status', 'admin_rejected')->count() / $orders) * 100 : 0;
        $deliveredOrders   = $orders > 0 ?  (Order::where('status', 'admin_delivered')->count() / $orders) * 100 : 0;

        $order_status = [
            'title'  => trans('provider.statistics.order_status'),
            'labels' => [trans('app.messages.status.labels.pending'), trans('app.messages.status.labels.admin_accept'),trans('app.messages.status.labels.admin_rejected'), trans('app.messages.status.labels.admin_delivered')],
            'series' => [
                [
                    'name' => trans('provider.statistics.order_status.pending'),
                    'type' => 'donut',
                    'data' => $pendingOrders,
                ],
                [
                    'name' => trans('provider.statistics.order_status.admin_accepted'),
                    'type' => 'donut',
                    'data' => $adminAcceptOrders,
                ],
                [
                    'name' => trans('provider.statistics.order_status.admin_rejected'),
                    'type' => 'donut',
                    'data' => $adminRejectOrders,
                ],
                [
                    'name' => trans('provider.statistics.order_status.admin_delivered'),
                    'type' => 'donut',
                    'data' => $deliveredOrders,
                ]
            ]
        ];

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
                'order_status'              => $order_status,
                'top_selling_product_chart' => $top_selling_product,
            ],
            'messages' => ""
        ]);
    }
}
