<?php

namespace App\Http\Controllers\Api\Dashboard\Permission;

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Permission\PermissionRequest;
use App\Http\Resources\Api\Dashboard\Permission\PermissionResource;
use App\Http\Resources\Api\Dashboard\Permission\PermissionIndexResource;
use App\Http\Requests\Api\Dashboard\Permission\UpdateAllPermissionsRequest;
use App\Http\Resources\Api\Dashboard\Permission\CustomPermissionSideBarResource;
use App\Http\Resources\Api\Dashboard\Permission\PermissionSideBarResource;
use Illuminate\Support\Arr;

class PermissionController extends Controller
{


    public function update_all_permissions(UpdateAllPermissionsRequest $request) {

        $data = $request->permissions;

        foreach ($data as $arr) {

            $dataArr = Arr::except($arr,['id']);

            // $languagesArr = [
            //     'dn' => [ 'title' => $arr['en']['title'] ],
            //     'ar' => [ 'title' => $arr['ar']['title']]
            // ];

            $row = Permission::where('id',$arr['id'])->first();

            if($row) {
                $row->update($dataArr);
            }
        }

        return response()->json(['status' => 'success', 'data' => null, 'message' => trans('dashboard/admin.actions.edited_successfully')]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lists = Permission::latest('id')->paginate(request()->per_page ?? 10);

        return PermissionIndexResource::collection($lists)->additional(['status' => 'success', 'message' => '']);
    }

    public function indexNotPaginated()
    {

        $dataArr = [];

        foreach (permissions_names() as $item) {
            $get_permissions = Permission::where('back_route_name', 'like', $item.'%')->get();
            // $dataArr[trans('dashboard.permissions.'.$item)] = PermissionIndexResource::collection($get_permissions);
            $dataArr[strtolower($item)] = PermissionIndexResource::collection($get_permissions);
        }

        return [
            'data' => $dataArr,
            'message' => '',
            'status' => 'success',

        ];

    }

    public function sideBarPermission()
    {

        $lang = app()->getLocale();

        $dataArr = [];

        $arr1 = [];

        if(in_array('slider',permissions_names_v2())) {
            $arr1 = [
                "label" => "",
                "icon" => "",
                "categories" => [new CustomPermissionSideBarResource('slider')]
            ];
        }

        ////////////////////////////////////////////////////////////////////

        $arr2 = [];

        if(in_array('categories',permissions_names_v2())) {
            $arr2 = [
                "label" => "",
                "icon" => "",
                "categories" => [new CustomPermissionSideBarResource('categories')]
            ];
        }

        ////////////////////////////////////////////////////////////////////

        $productSetting = ['colors','sizes','feature'];
        $productSettingArr = [];
        $arr3 = [];

        foreach (permissions_names_v2() as $item) {

            if(in_array($item,$productSetting)) {
                $productSettingArr[] = new CustomPermissionSideBarResource($item);
            }
        }

        $arr3 = [
            "label" => $lang == 'en' ? 'Product Settings' : 'اعدادات المنتجات',
            "icon" => "",
            "categories" => $productSettingArr
        ];


        // foreach (permissions_names_v2() as $item) {

        //     if(! in_array($item,$productSetting)) {
        //         $dataArr[] = [
        //             "label" => "",
        //             "icon" => "",
        //             "categories" => [
        //                 new CustomPermissionSideBarResource($item)
        //             ]
        //             // "categories" => [
        //             //     "icon"  => "fa-solid fa-image",
        //             //     "title" => trans('dashboard.permissions.'.$item),
        //             //     "permissions" => PermissionSideBarResource::collection(Permission::where('back_route_name', 'like', $item .'.index'.'%')->orWhere('back_route_name', 'like', $item .'.store'.'%')->get())
        //             // ]
        //         ];
        //     }
        // }


        // $dataArr[0] = $arr1;
        // $dataArr[1] = $arr2;

        if(array_key_exists('categories',$arr1) && count($arr1['categories']) > 0) {
            $dataArr[0] = $arr1;
        }

        if(array_key_exists('categories',$arr2) && count($arr2['categories']) > 0) {
            $dataArr[1] = $arr2;
        }

        if(array_key_exists('categories',$arr3) && count($arr3['categories']) > 0) {
            $dataArr[2] = $arr3;
        }

        return [
            'data' => $dataArr,
            'message' => '',
            'status' => 'success',

        ];

    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);

        return PermissionResource::make($permission)->additional(['status' => 'success', 'message' => '']);
    }

    public function update(PermissionRequest $request , $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->validated());
        return PermissionResource::make($permission)->additional(['status' => 'success', 'message' => '']);
    }

}
