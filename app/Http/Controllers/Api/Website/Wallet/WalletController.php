<?php

namespace App\Http\Controllers\Api\Website\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Website\Wallet\{ChargeRequest, WithdrawalRequest};
use App\Http\Resources\Api\Website\Wallet\{WalletTransactionResource};
use App\Models\{Order, Wallet, WalletTransaction, Withdrawal};
use App\Traits\OrderOperation;
use Illuminate\Http\Request;
use MyFatoorah\Library\MyFatoorah;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentEmbedded;
use MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus;
use Exception;
use DB;


class WalletController extends Controller
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
    
    public function index()
    {
        $transactions = WalletTransaction::where(['user_id' => auth('api')->id()])->take(4)->orderBy('id', 'desc')->get();
        return response()->json(['status' => 'success', 'message' => '', 'data' => [
            'transactions' => WalletTransactionResource::collection($transactions),
            'balance' => auth()->guard('api')->user()->wallet ? (float)auth()->guard('api')->user()->wallet->balance  : 0.0,
        ]], 200);
    }
    public function transactions()
    {
        $transactions = WalletTransaction::where(['user_id' => auth('api')->id()])->orderBy('id', 'desc')->paginate();

        $data['transactions'] = WalletTransactionResource::collection($transactions)->response()->getData(true);

        $data['balance'] = auth()->guard('api')->user()->wallet ? (float)auth()->guard('api')->user()->wallet->balance  : 0.0;

        return response()->json(['status' => 'success', 'message' => '', 'data' => $data]);
    }

    // public function charge(ChargeRequest $request)
    // {
    //     \DB::beginTransaction();
    //     try {
    //         $userWallet = Wallet::where('user_id', auth('api')->id())->firstOrCreate(['user_id' => auth('api')->id()]);
    //         $charge  = WalletTransaction::create(
    //             [
    //                 'user_id' => auth('api')->id(),
    //                 'type' => 'charge',
    //                 'balance_before' => $userWallet->balance ?$userWallet->balance :0, 
    //                 'balance_after' => $userWallet->balance + $request->amount,
    //                 'amount' => $request->amount,
    //                 'wallet_id' => $userWallet->id,
    //                 'transaction_id' => $request->transaction_id,
    //             ]
    //         );
    //         $userWallet->update([
    //             'balance' => $userWallet->balance + $request->amount
    //         ]);
    //         \DB::commit();
    //         return response()->json(['status' => 'success', 'message' => trans('app.messages.charge_wallet_successfully'), 'data' => new WalletTransactionResource($charge)]);
    //     } catch (\Exception $e) {
    //         \DB::rollback();
    //         dd($e);
    //         return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
    //     }
    // }

    public function cacheOutRequest(WithdrawalRequest $request)
    {
        try {
            $cacheOut   =  Withdrawal::create($request->validated() +  [
                'user_id' => auth('api')->id(),
                'currency' => 'SAR',
                'status' => 'pending',
            ]);
            return response()->json(['status' => 'success', 'message' => trans('app.messages.wait_withdrawal'), 'data' => null]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    public function charge(ChargeRequest $request)
    {
        \DB::beginTransaction();
        try {

            $userWallet = Wallet::where('user_id', auth('api')->id())->firstOrCreate(['user_id' => auth('api')->id()]);

            $charge  = WalletTransaction::create(
                [
                    'user_id' => auth('api')->id(),
                    'type' => 'charge',
                    'balance_before' => $userWallet->balance ?$userWallet->balance :0, 
                    'balance_after' => $userWallet->balance + $request->amount,
                    'amount' => $request->amount,
                    'wallet_id' => $userWallet->id,
                    'status' => 'pending'
                    // 'transaction_id' => $request->transaction_id,
                ]
            );

            $charge->update(['redirect_url' => $request->url]);
            
            ////////////////////////////////////////////////////////////////////////

            $paymentId = request('pmid') ?: 0;
            $sessionId = request('sid') ?: null;

            $chargeId  = $charge->id;
            $curlData = $this->getPayLoadData($chargeId);

            $mfObj   = new MyFatoorahPayment($this->mfConfig);
            $payment = $mfObj->getInvoiceURL($curlData, $paymentId, $chargeId, $sessionId);

            if(array_key_exists('invoiceId',$payment)) {
                $charge->update([
                    'invoice_id' => $payment['invoiceId'],
                    'transaction_id' => $payment['invoiceId'],
                ]);
            }

            $dataArr['payment'] = $payment;


            \DB::commit();
            
            return response()->json(['status' => 'success', 'message' => trans('app.messages.charge_wallet_successfully'), 'data' => $dataArr]);
            // return response()->json(['status' => 'success', 'message' => trans('app.messages.charge_wallet_successfully'), 'data' => new WalletTransactionResource($charge)]);


        } catch (\Exception $e) {
            \DB::rollback();
            dd($e);
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
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
    private function getPayLoadData($chargeId = null) {

        $callbackURL = route('api.myfatoorah_wallet.callback');

        // $ErrorUrl = route('api.myfatoorah.error');

        //You can get the data using the order object in your system
        $wallet_transaction = WalletTransaction::where('id',$chargeId)->firstOrFail();

        $ErrorUrl = $wallet_transaction->redirect_url.'?operation_type=fail&message=Payment-Failed-V1';

        return [
            'CustomerName'       => $wallet_transaction->client->fullname,
            'InvoiceValue'       => $wallet_transaction->amount,
            'DisplayCurrencyIso' => 'SAR',
            'CustomerEmail'      => $wallet_transaction->client->email,
            'CallBackUrl'        => $callbackURL,
            'ErrorUrl'           => $ErrorUrl,
            'MobileCountryCode'  => '+'.$wallet_transaction->client->phone_code,
            'CustomerMobile'     => $wallet_transaction->client->phone,
            'Language'           => 'en',
            'CustomerReference'  => $wallet_transaction->id,
            'SourceInfo'         => 'Laravel ' . app()::VERSION . ' - MyFatoorah Wallet Package V1' 
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

            $wallet_transaction = WalletTransaction::where('invoice_id',$data->InvoiceId)->first();

            if($wallet_transaction && $data->InvoiceStatus == 'Paid') {

                $wallet_transaction->update([
                    'status' => 'accepted',
                    'payment_id' => $paymentId
                ]);

                $userWallet = $wallet_transaction->wallet;

                if($userWallet) {

                    $userWallet->update([
                        'balance' => $userWallet->balance + $wallet_transaction->amount
                    ]);
    
                    $redirect_url = $wallet_transaction->redirect_url.'?operation_type=success';
                    return redirect()->to($redirect_url);
    
                } else {

                    $redirect_url = $wallet_transaction->redirect_url.'?operation_type=fail&message=Payment-Failed-V2';
                    return redirect()->to($redirect_url);
                }
                
                
            } else {

                $wallet_transaction->update([
                    'status' => 'failed',
                ]);

                $redirect_url = $wallet_transaction->redirect_url.'?operation_type=fail&message=Payment-Failed-V3';
                return redirect()->to($redirect_url);

            }

        } catch (Exception $ex) {

            $exMessage = __('myfatoorah.' . $ex->getMessage());
            $response  = ['IsSuccess' => 'false', 'Message' => $exMessage];

            return redirect('website/wallet-payment-operation?operation_type=fail&message=Payment-Failed-With-Error');

        }
        //return response()->json($response);
    }


    public function error() {

    }

    public function payment_operation(Request $request) {
        return $request->operation_type;
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
