<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Permission;
use App\Models\PermissionTranslation;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $routesNamesList = array();

        $routeCollection = Route::getRoutes();

        // foreach ($routeCollection as $value) {

        //     if($value->getActionName() != null && startsWith($value->getActionName(),'App\Http\Controllers\Api\Dashboard')) {

        //         $routeName = $value->getName();

        //         if($routeName && ! startsWith($routeName, "ignition")) {
        //             $routesNamesList[] = $routeName;
        //         }
        //     }
        // }

        foreach ($routeCollection as $index => $value) {

            if($value->getActionName() != null && startsWith($value->getActionName(),'App\Http\Controllers\Api\Dashboard') ) {

                $routeName = $value->getName();

                if(( ! str_contains($routeName, 'profile') || ! str_contains($routeName, 'permission'))) {

                    // if($routeName && ! startsWith($routeName, "ignition")) { }

                    if(str_contains($routeName, '.index')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.index' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/show-all';

                    } elseif(str_contains($routeName, '.store')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.store' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/add';

                    } elseif(str_contains($routeName, '.show')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.show' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/show';

                    } elseif(str_contains($routeName, '.update_')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.update_' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/edit';

                    } elseif(str_contains($routeName, '.update')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.update' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/edit';

                    } elseif(str_contains($routeName, '.destroy')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.destroy' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/delete';

                    } elseif(str_contains($routeName, '.get')) {

                        $routesNamesList[$index]['back_name'] = $routeName;

                        $subject = $routeName;
                        $search = '.get' ;
                        $trimmed = str_replace($search, '', $subject) ;

                        $routesNamesList[$index]['front_name'] = $trimmed.'/show';

                    }
                }

            }
        }

        Permission::where('id','>',0)->delete();
        PermissionTranslation::where('id','>',0)->delete();

        // $routesNamesList = array_unique($routesNamesList);

        foreach ($routesNamesList as $perm) {

            $permission_row = Permission::firstOrCreate([
                'front_route_name' => $perm['front_name'],
                'back_route_name' => $perm['back_name'],
            ])->id;

            foreach(config('translatable.locales') as $locale)
            {
                PermissionTranslation::Create([
                    'title' => ucfirst($perm['back_name']),
                    'locale' => $locale,
                    'permission_id' => $permission_row,
                ]);
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////

        // $routesNamesList = array();

        // $routeCollection = Route::getRoutes();

        // // echo "<table style='width:100%'>";
        // // echo "<tr>";
        // // echo "<td width='10%'><h4>HTTP Method</h4></td>";
        // // echo "<td width='10%'><h4>Route</h4></td>";
        // // echo "<td width='10%'><h4>Name</h4></td>";
        // // echo "<td width='70%'><h4>Corresponding Action</h4></td>";
        // // echo "</tr>";

        // foreach ($routeCollection as $value) {

        //     $routeName = $value->getName();

        //     if($routeName && ! startsWith($routeName, "ignition")) {
        //         $routesNamesList[] = $routeName;
        //     }

        //     // echo "<tr>";
        //     // echo "<td>" . $value->methods()[0] . "</td>";
        //     // echo "<td>" . $value->uri() . "</td>";
        //     // echo "<td>" . $value->getName() . "</td>";
        //     // //echo "<td>" . $value->getActionName() . "</td>";
        //     // echo "</tr>";

        // }

        // // echo "</table>";

        // // dd($routesNamesList);

        // Permission::where('id','>',0)->delete();
        // PermissionTranslation::where('id','>',0)->delete();

        // foreach ($routesNamesList as $perm) {

        //     $permission_row = Permission::firstOrCreate([
        //         'back_route_name' => $perm,
        //     ])->id;

        //     foreach(config('translatable.locales') as $locale)
        //     {
        //         PermissionTranslation::Create([
        //             'title' => ucfirst($perm),
        //             'locale' => $locale,
        //             'permission_id' => $permission_row,
        //         ]);
        //     }
        // }


        $permission_ids = Permission::pluck('id')->toArray();

        $role = Role::create(['en' => ['name' => 'admin'],'ar' => ['name' => 'admin']]);

        $role->permissions()->attach($permission_ids);

        // dd($routesNamesList,$permission_ids);


    }
}
