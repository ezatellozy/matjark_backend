<?php

namespace App\Http\Controllers\Api\Dashboard\Role;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\Role\{RoleRequest};
use App\Http\Resources\Api\Dashboard\Role\{RoleItemResource, RoleResource , TranslatedRoleResource};

class RoleController extends Controller
{


    public function role_names(Request $request)
    {
        $roles = Role::latest()->get();

        return RoleItemResource::collection($roles)->additional([
            'message' => '',
            'status' =>  'success'
        ]);
    }

    public function indexNotPaginated(Request $request)
    {
        $role = Role::latest()->get();
        return RoleResource::collection($role)->additional([
            'message' => '',
            'status' =>  'success'
        ]);
    }

    public function index(Request $request)
    {
        $role = Role::latest()->paginate($request->per_page ?? 10);
        return RoleResource::collection($role)->additional([
            'message' => '',
            'status' =>  'success'
        ]);
    }
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return TranslatedRoleResource::make($role)->additional(['status' => 'success', 'message' => '']);
    }


    public function store(RoleRequest $request)
    {
        // return ($request->validated()) ;
        $role = Role::create(array_except($request->validated(), ["permission_ids"]));
        $role->permissions()->attach($request->permission_ids);
        return response()->json(['status' => 'success', 'data' => null, 'message' =>  trans('dashboard/admin.actions.created_successfully')]);
    }
    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->update(array_except($request->validated(), ["permission_ids"]));
        $role->permissions()->sync($request->permission_ids);
        return response()->json(['status' => 'success', 'data' => null, 'message' =>  trans('dashboard/admin.actions.updated_successfully')]);
    }
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        if ($role->users->count() > 0) {
            return response()->json(['status' => 'success', 'data' => null, 'message' =>  trans('dashboard/admin.actions.cant_delete_this_role_because_user_used')], 422);
        }
        $role->delete();
        return response()->json(['status' => 'success', 'data' => null, 'message' =>  trans('dashboard/admin.actions.deleted_successfully')]);
    }
}
