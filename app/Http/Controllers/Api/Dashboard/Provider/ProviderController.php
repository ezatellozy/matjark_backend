<?php

namespace App\Http\Controllers\Api\Dashboard\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\Admin\AdminRequest;
use App\Http\Resources\Api\Provider\Admin\AdminResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $providers = User::where('user_type', 'provider')
            ->when($request->country_id && is_array($request->country_id), function ($q) use ($request) {
                $q->whereHas('country', function ($q) use ($request) {
                    $q->whereIn('country_id', $request->country_id);
                });
            })
            ->when($request->keyword, function($q) use($request){
                $q->where('fullname', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%');
            })
            ->latest()->paginate(10);

        return AdminResource::collection($providers)->additional(['status' => 'success', 'messages' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRequest $request)
    {
        DB::beginTransaction();
        try{
            $provider = User::create($request->safe()->except(['country_id', 'city_id'])+['user_type' => 'provider', 'uuid' => Str::uuid()]);
            $provider->profile()->create($request->safe()->only(['country_id', 'city_id']));

            DB::commit();
            return response()->json(['status' => 'success', 'data' => AdminResource::make($provider), 'messages' => trans('provider.create.success')]);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.create.fail')], 422);
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
        $provider = User::where('user_type', 'provider')->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => AdminResource::make($provider), 'messages' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRequest $request, $id)
    {
        DB::beginTransaction();
        try{
            $provider = User::where('user_type', 'provider')->findOrFail($id);
            $provider->update($request->safe()->except(['country_id', 'city_id']));
            $provider->profile()->update($request->safe()->only(['country_id', 'city_id']));
            DB::commit();
            return response()->json(['status' => 'success', 'data' => AdminResource::make($provider), 'messages' => trans('provider.update.success')]);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.update.fail')], 422);
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
        $provider = User::where('user_type', 'provider')->findOrFail($id);

        if ($provider->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('provider.delete.success')]);
        }
        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('provider.delete.fail'), 422]);
    }
}
