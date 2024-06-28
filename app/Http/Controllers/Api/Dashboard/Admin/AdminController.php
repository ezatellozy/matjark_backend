<?php

namespace App\Http\Controllers\Api\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Admin\AdminRequest;
use App\Http\Resources\Api\Dashboard\Admin\PermissionResource;
use App\Http\Resources\Api\Dashboard\Admin\AdminResource;
use App\Models\Permission;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admins = User::where('user_type', 'admin')
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

        return AdminResource::collection($admins)->additional(['status' => 'success', 'messages' => '']);
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
            $admin = User::create($request->safe()->except(['country_id', 'city_id'])+['user_type' => 'admin', 'uuid' => Str::uuid()]);
            $admin->profile()->create($request->safe()->only(['country_id', 'city_id']));

            DB::commit();
            return response()->json(['status' => 'success', 'data' => AdminResource::make($admin), 'messages' => trans('dashboard.create.success')]);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.create.fail')], 422);
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
        $admin = User::where('user_type', 'admin')->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => AdminResource::make($admin), 'messages' => '']);
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
            $admin = User::where('user_type', 'admin')->findOrFail($id);
            $admin->update($request->safe()->except(['country_id', 'city_id']));
            $admin->profile()->update($request->safe()->only(['country_id', 'city_id']));
            DB::commit();
            return response()->json(['status' => 'success', 'data' => AdminResource::make($admin), 'messages' => trans('dashboard.update.success')]);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
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
        $admin = User::where('user_type', 'admin')->findOrFail($id);

        if ($admin->delete()) {
            return response()->json(['status' => 'success', 'data' => null, 'messages' => trans('dashboard/api.delete.success')]);
        }

        return response()->json(['status' => 'fail', 'data' => null, 'messages' => trans('dashboard/api.delete.fail'), 422]);
    }

    public function getMyPermissions()
    {
        $user = auth()->guard('api')->user();

        $lang = app()->getLocale();

        if($user->user_type == 'supper_admin') {
            $permissions  =  Permission::get();
        } else {
            $permissions  =  $user->role ? @$user->role->permissions()->get() : [];
        }

        if ($user->role) {
            return PermissionResource::collection($permissions)->additional([
                "status"  => "success",
                "message" => ""
            ]);
        }
        return response()->json([
            "status"  => "success",
            "data"    => [],
            "message" => ""
            // "status"  => "fail",
            // "data"    => null,
            // "message" => $lang == 'en' ? 'You Not have any permissions' : 'لا تمتلك اي صلاحيات'
        ]);
    }


    

}
