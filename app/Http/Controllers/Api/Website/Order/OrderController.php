<?php

namespace App\Http\Controllers\Api\Website\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Order\CancelOrderRequest;
use App\Http\Requests\Api\Website\Order\{IsPaymentRequest, OrderRequest, ReorderRequest};
use App\Http\Resources\Api\Website\Order\{OrderResource, SimpleOrderResource};
use App\Models\{FlashSaleProduct, Order,Cart, Product, ProductDetails, User, WalletTransaction};
use App\Notifications\Api\App\Order\{ClientCancelNotification, ClientFinishNotification, PendingStatusNotification};
use App\Traits\OrderOperation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use MyFatoorah\Library\MyFatoorah;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentEmbedded;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use Exception;
use DB;

class OrderController extends Controller
{
    use OrderOperation;

    /**
     * @var array
     */
    public $mfConfig = [];

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Initiate MyFatoorah Configuration
     */
    public function __construct() {
        $this->mfConfig = [
            'apiKey'      => config('myfatoorah.api_key'),
            'isTest'      => config('myfatoorah.test_mode'),
            'countryCode' => config('myfatoorah.country_iso'),
        ];
    }

    public function index(Request $request)
    {
        //pending , admin_accept , admin_cancel , client_cancel  , admin_shipping , admin_delivered , client_finished

        $orders =   Order::where('user_id', auth('api')->id())->when($request->status, function ($q) use ($request) {
            switch ($request->status) {
                case 'delivered':
                    $q->where('status', 'admin_delivered');
                    break;
                case 'shipped':
                    $q->where('status', 'admin_shipping');
                    break;
                case 'not_paid':
                    $q->where(['status' => 'admin_shipping', 'is_payment' => 'not_paid']);
                    break;
                case 'processing':
                    $q->whereIn('status', ['pending', 'admin_accept']);
                    break;

                case 'canceled':
                    $q->whereIn('status', ['admin_cancel', 'client_cancel', 'admin_rejected']);
                    break;
                case 'return_products':
                        $q->where('status','returned_order');
                        // $q->where('status','admin_delivered');
                        $q->whereHas('returnOrder');
                        break;
            }
        })->orderBy('id', 'desc')->withTrashed()->paginate(6);
        $status = [
            [
                'key' => 'delivered',
                'value' => trans('app.messages.order.delivered'),
                'status' => ['admin_delivered']
            ],
            [
                'key' => 'shipped',
                'value' => trans('app.messages.order.shipped'),
                'status' => ['admin_shipping']

            ],
            [
                'key' => 'unpaid',
                'value' => trans('app.messages.order.unpaid'),
                'status' => ['admin_shipping']

            ],
            [
                'key' => 'processing',
                'value' => trans('app.messages.order.processing'),
                'status' => ['pending', 'admin_accept']

            ],
            [
                'key' => 'canceled',
                'value' => trans('app.messages.order.canceled'),
                'status' => ['client_cancel', 'admin_cancel', 'admin_rejected']

            ],
            [
                'key' => 'return_products',
                'value' => trans('app.messages.order.return_products'),
                'status' => []

            ],
        ];
        return response()->json(['data' => SimpleOrderResource::collection($orders), 'status' => 'success', 'message' => '', 'available_status' =>     $status]);
    }


    public function show($id)
    {
        $order =   Order::where(['id' => $id, 'user_id' => auth('api')->id()])->withTrashed()->firstOrFail();
        return response()->json(['data' => new OrderResource($order), 'status' => 'success', 'message' => '']);
    }


    public function show_by_marchent_order_id($marchent_order_id)
    {
        $order =   Order::where(['marchent_order_id' => $marchent_order_id, 'user_id' => auth('api')->id()])->withTrashed()->firstOrFail();
        $order->update(['is_payment' => 'paid', 'transactionId' => $marchent_order_id]);
        return response()->json(['data' => new OrderResource($order), 'status' => 'success', 'message' => '']);
    }


    public function store(OrderRequest $request)
    {

        \DB::beginTransaction();
        try {
            // info($request);

            $order =  $this->addProduct($request);

            if ($order->pay_type  == 'wallet') {

                WalletTransaction::create([
                    'user_id' => auth('api')->id(),
                    'wallet_id' => auth()->guard('api')->user()->wallet->id,
                    'order_id' => $order->id,
                    'balance_before' => auth()->guard('api')->user()->wallet->balance,
                    'balance_after' => (auth()->guard('api')->user()->wallet->balance  - $order->orderPriceDetail->total_price),
                    'amount' =>  $order->orderPriceDetail->total_price,
                    'type' => 'buying',
                ]);

                auth()->guard('api')->user()->wallet()->update(['balance' => (auth()->guard('api')->user()->wallet->balance  - $order->orderPriceDetail->total_price)]);
                $order->update(['is_payment' => 'paid']);

                // auth()->guard('api')->user()->cart()->delete();

                Cart::where('user_id',auth()->guard('api')->user()->id)->delete();


            } elseif ($order->pay_type  == 'card') {

                $paymentId = request('pmid') ?: 0;
                $sessionId = request('sid') ?: null;

                $orderId  = $order->id;
                $curlData = $this->getPayLoadData($orderId);

                $mfObj   = new MyFatoorahPayment($this->mfConfig);
                $payment = $mfObj->getInvoiceURL($curlData, $paymentId, $orderId, $sessionId);

                if(array_key_exists('invoiceId',$payment)) {
                    $order->update([
                        'invoice_id' => $payment['invoiceId']
                    ]);
                }

                $dataArr['payment'] = $payment;

                $order->update(['redirect_url' => $request->redirect_url]);

                Cart::where('user_id',auth()->guard('api')->user()->id)->delete();

            } else {
                Cart::where('user_id',auth()->guard('api')->user()->id)->delete();
            }


            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();

            if ($admins) {
                foreach ($admins as $admin) {
                    $admin->notify(new PendingStatusNotification($order, ['database', 'fcm']));
                }
            }

            $dataArr['order'] = new OrderResource($order->refresh());

            \DB::commit();

            return response()->json(['data' => $dataArr, 'status' => 'success', 'message' => trans('app.messages.added_successfully')]);


        } catch (\Exception $e) {
            \DB::rollback();
            // dd('error',$e->getLine(),$e->getMessage());

            \Log::info($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }



    public function isPayment(IsPaymentRequest $request, $id)
    {
        $order =   Order::where(['id' => $id, 'user_id' => auth('api')->id()])->firstOrFail();
        if ($order->pay_type != 'card') {
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.confirm_the_payment_method')]);
        }
        if ($order->is_payment == 'paid') {
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.the_order_has_been_prepaid')]);
        }

        \DB::beginTransaction();

        try {
            // $order->update(['is_payment' => 'paid', 'transactionId' => $request->transactionId]);
            // \DB::commit();
            // return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.paid_successfully')]);

            $paymentId = request('pmid') ?: 0;
            $sessionId = request('sid') ?: null;

            $orderId  = $order->id;
            $curlData = $this->getPayLoadData($orderId);

            $mfObj   = new MyFatoorahPayment($this->mfConfig);
            $payment = $mfObj->getInvoiceURL($curlData, $paymentId, $orderId, $sessionId);

            if(array_key_exists('invoiceId',$payment)) {
                $order->update([
                    'invoice_id' => $payment['invoiceId']
                ]);
            }

            $dataArr['payment'] = $payment;

            $order->update(['redirect_url' => $request->redirect_url]);

            $dataArr['order'] = new OrderResource($order->refresh());

            \DB::commit();

            return response()->json(['data' => $dataArr, 'status' => 'success', 'message' => trans('app.messages.added_successfully')]);


        } catch (\Exception $e) {
            \DB::rollback();
            // dd($e);
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function cancel(CancelOrderRequest $request, $order_id)
    {
        $order = Order::where(['id' => $order_id, 'user_id' => auth('api')->id(), 'status' => 'pending'])->firstOrFail();
        // dd($request->all());
        try {
            $order->update([
                'status' => 'client_cancel',
                'order_status_times' => ['client_cancel' => date("Y-m-d H:i")],
                'user_cancel_reason' => $request->user_cancel_reason,

            ]);
            // add total price to client wallet if payment   / wallet , card and paid


            if (in_array($order->pay_type,  ['card', 'wallet']) && $order->is_payment == 'paid') {
                WalletTransaction::create([
                    'user_id' => auth('api')->id(),
                    'wallet_id' => auth()->guard('api')->user()->wallet->id,
                    'order_id' => $order->id,
                    'balance_before' => auth()->guard('api')->user()->wallet->balance,
                    'balance_after' => (auth()->guard('api')->user()->wallet->balance  + $order->orderPriceDetail->total_price),
                    'amount' =>  $order->orderPriceDetail->total_price,
                    'type' => 'charge',
                ]);
                auth()->guard('api')->user()->wallet()->update(['balance' => (auth()->guard('api')->user()->wallet->balance + $order->orderPriceDetail->total_price)]);
            }
            // return quantity
            $orderProducts = $order->orderProducts;
            if ($orderProducts) {
                foreach ($orderProducts as $order_product) {
                    ProductDetails::where('id', $order_product->product_detail_id)->increment('quantity', $order_product->quantity);
                    FlashSaleProduct::where(['id' => $order_product->flash_sale_product_id])->decrement('sold', $order_product->quantity);
                }
            }

            // notification for admin
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            if ($admins) {
                foreach ($admins as $admin) {
                    // $admin->notify(new ClientCancelNotification($order, ['database', 'fcm']));
                    $admin->notify(new ClientCancelNotification($order, ['database', 'fcm']));
                }
            }
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.cancel_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }


    public function finished(Request $request, $order_id)
    {
        $order = Order::where(['id' => $order_id, 'user_id' => auth('api')->id(), 'status' => 'admin_delivered', 'is_payment' => 'paid'])->firstOrFail();
        try {
            $order->update([
                'status' => 'client_finished',
                'order_status_times' => ['client_finished' => date("Y-m-d H:i")],
            ]);
            //  notify for admin
            $admins = User::whereIn('user_type', ['admin', 'superadmin'])->get();
            if ($admins) {
                foreach ($admins as $admin) {
                    $admin->notify(new ClientFinishNotification($order, ['database', 'fcm']));
                }
            }
            return response()->json(['data' => new OrderResource($order->refresh()), 'status' => 'success', 'message' => trans('app.messages.finished_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }
    }

    public function reorder(ReorderRequest $request)
    {
        \DB::beginTransaction();

        try {

            $order = Order::where(['id' => $request->order_id, 'user_id' => auth('api')->id()])->firstOrFail();

            $orderItems = $order->orderProducts;

            $cart = Cart::where(['user_id' => auth('api')->id(), 'guest_token' => null])->firstOrCreate([
                'user_id' => auth('api')->id(),
                'guest_token' => null,
            ]);

            foreach ($orderItems as $orderItem) {
                if ($cart->cartProducts()->where(['product_detail_id' => $orderItem->product_detail_id, 'offer_id' => $orderItem->offer_id, 'flash_sale_product_id' => $orderItem->flash_sale_product_id])->exists()) {
                    $cart->cartProducts()->where(['product_detail_id' => $orderItem->product_detail_id, 'offer_id' => $orderItem->offer_id, 'flash_sale_product_id' => $orderItem->flash_sale_product_id])->increment('quantity', $orderItem->quantity);
                } else {
                    $cart->cartProducts()->create([
                        'product_detail_id' => $orderItem->product_detail_id,
                        'quantity' => $orderItem->quantity,
                        'offer_id' => $orderItem->offer_id,
                        'flash_sale_product_id' => $orderItem->flash_sale_product_id
                    ]);
                }
            }
            \DB::commit();
            return response()->json(['data' =>null, 'status' => 'success', 'message' => trans('app.messages.send_order_to_cart_successfully')]);
        } catch (\Exception $e) {
            \DB::rollback();
             dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 500);
        }

    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how to map order data to MyFatoorah
     * You can get the data using the order object in your system
     *
     * @param int|string $orderId
     *
     * @return array
     */
    private function getPayLoadData($orderId = null) {

        $callbackURL = route('api.myfatoorah.callback');

        // $ErrorUrl = route('api.myfatoorah.error');

        //You can get the data using the order object in your system
        $order = Order::findOrFail($orderId);

        $ErrorUrl = $order->redirect_url.'?operation_type=fail&message=Payment-Failed&order_id='.$order->id;

        return [
            'CustomerName'       => $order->client->fullname,
            'InvoiceValue'       => $order->orderPriceDetail->total_price,
            'DisplayCurrencyIso' => 'SAR',
            'CustomerEmail'      => $order->client->email,
            'CallBackUrl'        => $callbackURL,
            'ErrorUrl'           => $ErrorUrl,
            'MobileCountryCode'  => '+'.$order->client->phone_code,
            'CustomerMobile'     => $order->client->phone,
            'Language'           => 'en',
            'CustomerReference'  => $orderId,
            'SourceInfo'         => 'Laravel ' . app()::VERSION . ' - MyFatoorah Package V1'
        ];
    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Get MyFatoorah Payment Information
     * Provide the callback method with the paymentId
     *
     * @return Response
     */
    public function callback() {

        try {

            $paymentId = request('paymentId');

            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data  = $mfObj->getPaymentStatus($paymentId, 'PaymentId');

            $message = $this->getTestMessage($data->InvoiceStatus, $data->InvoiceError);

            // $response = ['IsSuccess' => true, 'Message' => $message, 'Data' => $data];

            $order = Order::where('invoice_id',$data->InvoiceId)->first();

            if($order && $data->InvoiceStatus == 'Paid') {

                $order->update([
                    'is_payment' => 'paid',
                    'payment_id' => $paymentId
                ]);

                // auth()->guard('api')->user()->cart()->delete();

                Cart::where('user_id',$order->user_id)->delete();

                $redirect_url = $order->redirect_url.'?operation_type=success&message=Payment-Completed-Successfully&order_id='.$order->id;
                return redirect()->to($redirect_url);

                // return redirect('website/payment-operation?operation_type=success&message=Payment-Completed-Successfully');

            } else {

                $redirect_url = $order->redirect_url.'?operation_type=fail&message=Payment-Failed&order_id='.$order->id;
                return redirect()->to($redirect_url);

                // return redirect('website/payment-operation?operation_type=fail&message=Payment-Failed');

            }

        } catch (Exception $ex) {

            $exMessage = __('myfatoorah.' . $ex->getMessage());
            $response  = ['IsSuccess' => 'false', 'Message' => $exMessage];

            return redirect('website/payment-operation?operation_type=fail&message=Payment-Failed-With-Error');

        }
        //return response()->json($response);
    }


    public function error() {

    }

    public function payment_operation(Request $request) {
        return $request->operation_type;
    }

//-----------------------------------------------------------------------------------------------------------------------------------------

    /**
     * Example on how the webhook is working when MyFatoorah try to notify your system about any transaction status update
     */
    public function webhook(Request $request) {
        try {
            //Validate webhook_secret_key
            $secretKey = config('myfatoorah.webhook_secret_key');
            if (empty($secretKey)) {
                return response(null, 404);
            }

            //Validate MyFatoorah-Signature
            $mfSignature = $request->header('MyFatoorah-Signature');
            if (empty($mfSignature)) {
                return response(null, 404);
            }

            //Validate input
            $body  = $request->getContent();
            $input = json_decode($body, true);
            if (empty($input['Data']) || empty($input['EventType']) || $input['EventType'] != 1) {
                return response(null, 404);
            }

            //Validate Signature
            if (!MyFatoorah::isSignatureValid($input['Data'], $secretKey, $mfSignature, $input['EventType'])) {
                return response(null, 404);
            }

            //Update Transaction status on your system
            $result = $this->changeTransactionStatus($input['Data']);

            return response()->json($result);
        } catch (Exception $ex) {
            $exMessage = __('myfatoorah.' . $ex->getMessage());
            return response()->json(['IsSuccess' => false, 'Message' => $exMessage]);
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
    private function changeTransactionStatus($inputData) {
        //1. Check if orderId is valid on your system.
        $orderId = $inputData['CustomerReference'];

        //2. Get MyFatoorah invoice id
        $invoiceId = $inputData['InvoiceId'];

        //3. Check order status at MyFatoorah side
        if ($inputData['TransactionStatus'] == 'SUCCESS') {
            $status = 'Paid';
            $error  = '';
        } else {
            $mfObj = new MyFatoorahPaymentStatus($this->mfConfig);
            $data  = $mfObj->getPaymentStatus($invoiceId, 'InvoiceId');

            $status = $data->InvoiceStatus;
            $error  = $data->InvoiceError;
        }

        $message = $this->getTestMessage($status, $error);

        //4. Update order transaction status on your system
        return ['IsSuccess' => true, 'Message' => $message, 'Data' => $inputData];
    }


//-----------------------------------------------------------------------------------------------------------------------------------------
    private function getTestMessage($status, $error) {
        if ($status == 'Paid') {
            return 'Invoice is paid.';
        } else if ($status == 'Failed') {
            return 'Invoice is not paid due to ' . $error;
        } else if ($status == 'Expired') {
            return $error;
        }
    }

//-----------------------------------------------------------------------------------------------------------------------------------------


}
