<?php

namespace App\Http\Controllers\Api\App\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\App\Address\{AddressRequest, IsDefaultRequest};
use App\Http\Resources\Api\App\Address\{AddressResource, SimpleAddressResource};
use App\Models\{Address};
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $addresses =   Address::where('user_id', auth('api')->id())->latest()->get();
        return (AddressResource::collection($addresses))->additional(['status' => 'success', 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    private  function checkAddresses($is_default, $address_id = null)
    {
        if ($is_default == 1) {
            $address = Address::where(['user_id' => auth('api')->id(), 'is_default' => 1])->where('id', '!=', $address_id)->update(['is_default' => 0]);
        }
    }
    public function is_default(IsDefaultRequest $request, $id)
    {
        $address = Address::where([
            'id' => $id,
            'user_id' => auth('api')->id(),
        ])->firstOrFail();
        try {
            $this->checkAddresses($request->is_default, $address->id);
            $address->update($request->validated());
            return response()->json(['data' => null, 'status' => 'success', 'message' => trans('app.messages.edited_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }
    public function store(AddressRequest $request)
    {
        try {
            $this->checkAddresses($request->is_default);
            $address =   Address::create($request->validated() + ['user_id' => auth('api')->id()]);
            return response()->json(['data' => new AddressResource($address), 'status' => 'success', 'message' => trans('app.messages.added_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $address = Address::where([
            'id' => $id,
            'user_id' => auth('api')->id(),
        ])->firstOrFail();
        return response()->json(['data' => new AddressResource($address), 'status' => 'success', 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddressRequest $request, $id)
    {
        $address = Address::where([
            'id' => $id,
            'user_id' => auth('api')->id(),
        ])->firstOrFail();
        try {
            $this->checkAddresses($request->is_default, $address->id);
            $address->update($request->validated());
            return response()->json(['data' => new AddressResource($address->refresh()), 'status' => 'success', 'message' => trans('app.messages.edited_successfully')]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::where([
            'id' => $id,
            'user_id' => auth('api')->id(),
        ])->firstOrFail();
        try {
            $address->delete();
            $addresses =   Address::where('user_id', auth('api')->id())->latest()->get();
            return (SimpleAddressResource::collection($addresses))->additional(['status' => 'success', 'message' => trans('app.messages.deleted_successfully')]);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::info($e->getMessage());
            return response()->json(['status' => 'fail', 'data' => null, 'message' => trans('app.messages.something_went_wrong_please_try_again')], 422);
        }
    }
}
