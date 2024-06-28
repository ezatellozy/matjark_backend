<?php
// namespace App\Http;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\File as File;
use Illuminate\Support\Facades\Notification as Notification;
use LaravelFCM\Facades\FCM as FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Models\{CartProduct, Category, Company, Device, Setting, User, Driver, FavouriteProduct, Feature, FlashSale, Offer, OrderRate, Permission, PointOffer};
use App\Jobs\{SendOrderRequestToDriver, SendFCMNotification};
use App\Notifications\General\{FCMNotification};
use App\Services\SMSService;
use GuzzleHttp\Client;
use App\Jobs\{UpdateTempWallet};

function startsWith($string, $startString)
{
  $len = strlen($startString);
  return substr($string, 0, $len) === $startString;
}

function permissions_names() {

    $all = Permission::pluck('back_route_name')->toArray();

    $permissions_names = [];

    foreach ($all as $item) {
        $arr = explode('.', $item);
        $permissions_names[] = $arr[0];
    }

    $permissions_names = array_unique($permissions_names);

    return $permissions_names;
}


function permissions_names_v2() {

    $user = auth()->guard('api')->user();

    if($user->user_type == 'supper_admin') {
        $permissionsArr  =  Permission::pluck('id')->toArray();
    } else {
        $permissionsArr  =  $user->role ? @$user->role->permissions->pluck('id')->toArray() : [];
    }

    $all = Permission::whereIn('id',$permissionsArr)->pluck('back_route_name')->toArray();

    $permissions_names = [];

    foreach ($all as $item) {
        $arr = explode('.', $item);

        // if(str_contains($arr[0], '.index') || str_contains($arr[0], '.store')) {
        //     $permissions_names[] = $arr[0];
        // }

        $permissions_names[] = $arr[0];

    }

    $permissions_names = array_unique($permissions_names);

    return $permissions_names;
}



function orderStatusV2($statusInArray,  $currentStatus, $orderStatusTimes)
{
    if ($statusInArray == $currentStatus) {
        return true;
    } else {
        return strpos($orderStatusTimes, $statusInArray) ? true : false;
    }
}


//return Settings
function setting($attr)
{
    if (\Schema::hasTable('settings')) {

        $phone = null;
        // $phone = $attr;
        // if ($attr == 'phone') {
        //     $attr = 'phones';
        // }
        $setting = Setting::where('key', $attr)->first() ?? [];
        if ($attr == 'project_name') {
            return !empty($setting) ? $setting->value : 'Alamyia';
        }
        if ($attr == 'logo') {
            return !empty($setting) ? asset('storage/images/setting') . "/" . $setting->value : asset('dashboardAssets/images/icons/logo_sm.png');
        }
        if ($phone == 'phone') {
            return !empty($setting) && $setting->value ? json_decode($setting->value)[0] : null;
        } elseif ($phone == 'phones') {
            return !empty($setting) && $setting->value ? implode(",", json_decode($setting->value)) : null;
        }
        if (!empty($setting)) {
            return $setting->value;
        }
        return false;
    }
    return false;
}

// Get Distance
function distance($startLat, $startLng, $endLat, $endLng, $unit = "K")
{
    // $unit = M --> Miles
    // $unit = K --> Kilometers
    // $unit = N --> Nautical Miles

    $startLat = (float) $startLat;
    $startLng = (float) $startLng;
    $endLat = (float) $endLat;
    $endLng = (float) $endLng;

    $theta = $startLng - $endLng;
    $dist = sin(deg2rad($startLat)) * sin(deg2rad($endLat)) + cos(deg2rad($startLat)) * cos(deg2rad($endLat)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function generate_unique_code($length, $model, $col = 'code', $type = 'numbers', $letter_type = 'all')
{
    if ($type == 'numbers') {
        $characters = '0123456789';
    } else {
        switch ($letter_type) {
            case 'all':
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 'lower':
                $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
                break;
            case 'upper':
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;

            default:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }
    }
    $generate_random_code = '';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $generate_random_code .= $characters[rand(0, $charactersLength - 1)];
    }
    if ($model::where($col, $generate_random_code)->exists()) {
        generate_unique_code($length, $model, $col, $type);
    }
    return $generate_random_code;
}


// Get Drivers
function getOtherDrivers($order, $notified_drivers, $number_of_drivers = 0)
{
    $number_of_drivers = $number_of_drivers + (int)convertArabicNumber(setting('number_drivers_to_notify'));


    $drivers = Driver::whereHas('user', function ($q) use ($order) {
        $q->available()->whereHas('profile', function ($q) {
            $q->whereNotNull('profiles.last_login_at');
        })->whereHas('devices')/*->withCount(['driverOrders','driverOrders as driver_orders_count' => function ($q) {
            $q->whereNotNull('orders.finished_at');
        }])*/
            // ->whereIn('users.id',online_users()->pluck('id'))
            /*->whereHas('car',function ($q) use($order) {
            $q->where('cars.car_type_id',$order->car_type_id);
        })*/->whereHas('car')->whereDoesntHave('driverOffers', function ($q) use ($order) {
                $q->where('order_offers.order_id', $order->id);
            });
    })->whereIn('driver_type', [$order->order_type, 'both'])->where(function ($q) {
        $q->where(function ($q) {
            $q->where('is_on_default_package', false)->whereHas('subscribedPackage', function ($q) {
                $q->whereDate('end_at', ">=", date("Y-m-d"))->where('is_paid', 1);
            });
        })/*->orWhere(function ($q) {
            $q->where(function ($q) {
                $q->where('is_on_default_package',true)->where('free_order_counter',"<",((int)setting('number_of_free_orders_on_default_package')))->orWhere(function ($q) {
                   $q->where('is_on_default_package',true)->whereHas('user',function ($q) {
                       $q->where('wallet',">",-(setting('min_wallet_to_recieve_order') ?? 10));
                   });
               });
            });
        })*/->orWhereHas('user', function ($q) {
            $q->where('is_with_special_needs', true);
        });
    })->when($order->start_lat && $order->start_lng, function ($q) use ($order) {
        $q->nearest($order->start_lat, $order->start_lng);
    })->when($number_of_drivers > 0, function ($q) use ($number_of_drivers) {
        $q->take($number_of_drivers);
    })->get();

    if ($drivers) {
        $drivers_ids_array = $drivers->pluck('user_id')->toArray();
        $db_drivers = User::whereIn('id', $drivers_ids_array)->get();
        $notified_drivers = $db_drivers->mapWithKeys(function ($item) use ($order) {
            $count = @optional($item->orderNotifiedDrivers()->firstWhere('driver_order.order_id', $order->id))->pivot->notify_number ?? 0;
            // dump($count);
            $total_drivers = [];
            if ($count >= ((int)convertArabicNumber(setting('driver_notify_count_to_refuse')) ?? 2)) {
                $total_drivers[$item['id']] = ['status' => 'refuse_reply', 'notify_number' => $count];
            } else {
                $total_drivers[$item['id']] = ['status' => 'notify', 'notify_number' => $count];
            }
            return $total_drivers;
        })->toArray();

        $order->driverNotifiedOrders()->syncWithoutDetaching($notified_drivers);
        $new_drivers = $order->driverNotifiedOrders()->where('driver_order.status', 'notify')->pluck('users.id')->toArray();
        $db_drivers = User::whereIn('id', $new_drivers)->get();
        $minutes = ((int)convertArabicNumber(setting('waiting_time_for_driver_response'))) ? ((int)convertArabicNumber(setting('waiting_time_for_driver_response'))) : 1;
        $fcm_data = [
            'title' => trans('dashboard.fcm.new_order_title'),
            'body' => trans('dashboard.fcm.new_order_body', ['client' => $order->fullname, 'order_type' => trans('dashboard.order.order_types.' . $order->order_type)]),
            'notify_type' => 'new_order',
            'order_id' => $order->id,
            'order_type' => $order->order_type,
        ];
        // pushFcmNotes($fcm_data,$drivers_ids_array,'\\App\\Models\\Driver');
        SendFCMNotification::dispatch($fcm_data, $new_drivers)->onQueue('wallet');
        Notification::send($db_drivers, new FCMNotification($fcm_data, ['database']));
        SendOrderRequestToDriver::dispatch($order, array_merge($new_drivers, $notified_drivers),  $number_of_drivers)->delay(now()->addMinutes($minutes));
    }
}


function sendNotify($user)
{
    try {
        if ($user->roles()->exists()) {
            $user->notify(new RegisterUser($user));
        } else {
            $user->notify(new VerifyApiMail($user));
        }
        $msg = [trans('dashboard.messages.success_add_send'), 1];
    } catch (\Exception $e) {
        $msg = [trans('dashboard.messages.success_add_not_send'), 0];
    }
    return $msg;
}


function uploadImg($files, $url = 'images', $key = 'image', $width = null, $height = null)
{
    $dist = storage_path('app/public/' . $url . "/");

    if ($url != 'images' && !File::isDirectory(storage_path('app/public/images/' . $url . "/"))) {
        File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR), 0777, true);
        $dist = storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR);
    } elseif (File::isDirectory(storage_path('app/public/images/' . $url . "/"))) {
        $dist = storage_path('app/public/images/' . $url . "/");
    }
    
    $image = "";

    if (!is_array($files)) {
        $dim = getimagesize($files);

        if($dim){
            $width = $width ?? $dim[0];
            $height = $height ?? $dim[1];
        } else {
            $width = 0;
            $height = 0;
        }

    }

    $image_name = uniqid() . '_'.time() . '.webp';

    if (gettype($files) == 'array') {
        $image_name = [];
        foreach ($files as $img) {
            $dim = getimagesize($img);
            $width = $width ?? $dim[0];
            $height = $height ?? $dim[1];

            if ($img && $dim['mime'] != "image/gif") {
                Image::make($img)->resize($width, $height, function ($cons) {
                    $cons->aspectRatio();
                })->encode('webp')
                // ->toWebp()
                // ->save($dist . $img->hashName());
                // ->save($dist . 'image.webp');
                ->save($dist .   $image_name);
                $image_name[][$key] = $img->hashName();
            } elseif ($img && $dim['mime'] == "image/gif") {
                $image_name = uploadGIFImg($img, $dist);
            }
        }
    } elseif ($dim && $dim['mime'] == "image/gif") {
        $image_name = uploadGIFImg($files, $dist);
    } else {
        if($width > 0 && $height > 0) {
            Image::make($files)->resize($width, $height, function ($cons) {
                $cons->aspectRatio();
            })->encode('webp')
            // ->toWebp()->save($dist . '/image.webp');
            // ->save($dist . $files->hashName());
            // ->toWebp()
            ->save($dist .  $image_name);
            // ->save($dist . $files->hashName());

        } else {
            Image::make($files)->encode('webp')
            // ->toWebp()->save($dist . '/image.webp');
            // ->toWebp()
            ->save($dist . $image_name);
            // ->save($dist . $files->hashName());
        }

        // $image = $files->hashName();

        if (!File::isDirectory(storage_path('app/public/images/' . $url . "/crop" . "/"))) {
            File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR . 'crop' . DIRECTORY_SEPARATOR), 0777, true);
        }
        Image::make($files)->widen(200)->encode('webp')
        // ->toWebp()->save($dist . '/image.webp');
        // ->toWebp()
        ->save($dist . 'crop/' .  $image_name );
        // ->save($dist . 'crop/' . $files->hashName());
        // ->save($dist . '/image.webp');
    }
    return $image_name;
}

function uploadGIFImg($gif_image, $dist)
{
    $file_name = Str::uuid() . "___" . $gif_image->getClientOriginalName();
    if ($gif_image->move($dist, $file_name)) {
        return $file_name;
    }
}

function uploadFile($files, $url = 'files', $key = 'file', $model = null)
{
    $dist = storage_path('app/public/' . $url);
    if ($url != 'images' && !File::isDirectory(storage_path('app/public/files/' . $url . "/"))) {
        File::makeDirectory(storage_path('app/public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR), 0777, true);
        $dist = storage_path('app/public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $url . DIRECTORY_SEPARATOR);
    } elseif (File::isDirectory(storage_path('app/public/files/' . $url . "/"))) {
        $dist = storage_path('app/public/files/' . $url . "/");
    }
    $file = '';

    if (gettype($files) == 'array') {
        $file = [];
        foreach ($files as $new_file) {
            $file_name = time() . "___file_" . $new_file->getClientOriginalName();
            if ($new_file->move($dist, $file_name)) {
                $file[][$key] = $file_name;
            }
        }
    } else {
        $file = $files;
        $file_name = time() . "___file_" . $file->getClientOriginalName();
        if ($file->move($dist, $file_name)) {
            $file =  $file_name;
        }
    }

    return $file;
}

function convertArabicNumber($number)
{
    $arabic_array = ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'];
    return strtr($number, $arabic_array);
}

function filter_mobile_number($phone)
{
    $phone =  convertArabicNumber($phone);
    $first_number = substr($phone, 0, 1);
    if ($first_number == '0') {
        $phone = substr($phone, 1);
    }
    return $phone;
}
// function filter_mobile_number($mob_num)
// {
//     $mob_num = convertArabicNumber($mob_num);
//     $first_3_val = substr($mob_num, 0, 3);
//     $first_4_val = substr($mob_num, 0, 4);
//     $sixth_val = substr($mob_num, 0, 6);
//     $first_val = substr($mob_num, 0, 1);
//     $mob_number = 0;
//     $val = 0;
//     if ($sixth_val == "009665") {
//         $val = null;
//         $mob_number = substr($mob_num, 2, 12);
//     } elseif ($sixth_val == "009660") {
//         $val = 966;
//         $mob_number = substr($mob_num, 6, 14);
//     } elseif ($first_3_val == "+96") {
//         $val = "966";
//         $mob_number = substr($mob_num, 4);
//     } elseif ($first_4_val == "9660") {
//         $val = "966";
//         $mob_number = substr($mob_num, 4);
//     }elseif ($first_3_val == "966") {
//         $val = null;
//         $mob_number = $mob_num;
//     } elseif ($first_val == "5") {
//         $val = "966";
//         $mob_number = $mob_num;
//     } elseif ($first_3_val == "009") {
//         $val = "9";
//         $mob_number = substr($mob_num, 4);
//     } elseif ($first_val == "0") {
//         $val = "966";
//         $mob_number = substr($mob_num, 1, 9);
//     } else {
//         $val = "966";
//         $mob_number = $mob_num;
//     }

//     $real_mob_number = $val . $mob_number;
//     return $real_mob_number;
// }


// /**
//  * Push Notifications to phone FCM
//  *
//  * @param  array $fcmData
//  * @param  array $userIds
//  */
// function pushFcmNotes($fcmData, $userIds, $model = '\\App\\Models\\Device')
// {
//     $send_process = [];
//     $fail_process = [];

//     if (is_array($userIds) && !empty($userIds)) {
//         $number_of_drivers = null;
//         if ($model == '\\App\\Models\\Driver') {
//             $model = '\\App\\Models\\Device';
//             $number_of_drivers = 1;
//         }
//         $devices = $model::whereIn('user_id', $userIds)/*->distinct('device_token')*/->latest()->when($number_of_drivers, function ($q) use ($number_of_drivers) {
//             $q->take($number_of_drivers);
//         })->get();
//         $ios_devices = array_filter($devices->where('type', 'ios')->pluck('device_token')->toArray());
//         $android_devices = array_filter($devices->where('type', 'android')->pluck('device_token')->toArray());

//         $optionBuilder = new OptionsBuilder();
//         $optionBuilder->setTimeToLive(60 * 20);

//         $notificationBuilder = new PayloadNotificationBuilder($fcmData['title']);
//         $notificationBuilder->setBody($fcmData['body'])
//             ->setSound('default');

//         $dataBuilder = new PayloadDataBuilder();
//         $dataBuilder->addData($fcmData);

//         $option       = $optionBuilder->build();
//         $data         = $dataBuilder->build();
//         if (count($ios_devices)) {
//             $notification = $notificationBuilder->build();
//             // You must change it to get your tokens
//             $downstreamResponse = FCM::sendTo($ios_devices, $option, $notification, $data);
//             Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
//             // return $downstreamResponse;
//             $send_process[] = $downstreamResponse->numberSuccess();
//         }
//         if (count($android_devices)) {
//             $notification = null;
//             // You must change it to get your tokens
//             $downstreamResponse = FCM::sendTo($android_devices, $option, $notification, $data);
//             Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
//             // return $downstreamResponse;
//             $send_process[] = $downstreamResponse->numberSuccess();
//             // code...
//         }
//         return count($send_process);
//     }
//     return "No Users";
// }

/**
 * Push Notifications to phone FCM
 *
 * @param  array $fcmData
 * @param  array $userIds
 */
function pushFcmNotes($fcmData, $userIds, $model = '\\App\\Models\\Device')
{
  $send_process = [];
  $fail_process = [];

  if (is_array($userIds) && !empty($userIds)) {
      $number_of_drivers = null;
      if ($model == '\\App\\Models\\Driver') {
          $model = '\\App\\Models\\Device';
          $number_of_drivers = 1;
      }
      $devices = $model::whereIn('user_id', $userIds)/*->distinct('device_token')*/->latest()->when($number_of_drivers, function ($q) use ($number_of_drivers) {
          $q->take($number_of_drivers);
      })->get()->pluck('device_token')->toArray();
      // $ios_devices = array_filter($devices->where('type', 'ios')->pluck('device_token')->toArray());
      // $android_devices = array_filter($devices->where('type', 'android')->pluck('device_token')->toArray());

      $optionBuilder = new OptionsBuilder();
      $optionBuilder->setTimeToLive(60 * 20);

      $notificationBuilder = new PayloadNotificationBuilder($fcmData['title']);
      $notificationBuilder->setBody($fcmData['body'])
          ->setSound('default');

      $dataBuilder = new PayloadDataBuilder();
      $dataBuilder->addData($fcmData);

      $option       = $optionBuilder->build();
      $data         = $dataBuilder->build();
      // if (count($ios_devices)) {
      if (count($devices) > 0) {
          $notification = $notificationBuilder->build();
          // You must change it to get your tokens
          foreach ($devices as $device) {
              if(!$device){
                continue ;
              }
              $downstreamResponse = FCM::sendTo($device, $option, $notification, $data);
                if($downstreamResponse){
                        Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
                        $send_process[] = $downstreamResponse->numberSuccess();
                        return count($send_process);
                }
          }
          // return $downstreamResponse;
        //   $send_process[] = $downstreamResponse->numberSuccess();
          // }
          // if (count($android_devices)) {
          //     $notification = null;
          //     // You must change it to get your tokens
          //     $downstreamResponse = FCM::sendTo($android_devices, $option, $notification, $data);
          //     Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
          //     // return $downstreamResponse;
          //     $send_process[] = $downstreamResponse->numberSuccess();
          //     // code...
          // }
      }

  }
  return "No Users";
}

// HISMS
// function send_sms($mobile, $msg)
// {
//     $sender_name = str_replace(' ', '%20', setting('project_name'));
//     $msg = str_replace(' ', '%20', $msg);
//     $sender_data = [
//         'username' => setting('sms_username'),
//         'password' => setting('sms_password'),
//         'sender_name' => setting('sms_sender_name') ?? $sender_name,
//     ];
//     $send_data = [
//         'message' => $msg,
//         'numbers' => $mobile
//     ];
//     $date_time = [
//         'date' => date('Y-m-d'),
//         'time' => date("H:i")
//     ];
//     return SMSService::send($sender_data, $send_data, $date_time, setting('sms_provider'));
// }



// function send_smsForReply($mobile, $msg){
//     $mobile = '20'.$mobile;
//     $client = new Client();
//     $data = [
//         'headers' => [
//               'Accept' => 'application/json',
//               'Content-Type' => 'application/json',
//           ],
//           'query' => [
//               'environment' => 1,
//                 'username' => 'b7dc3bd52a4d92613a6bb9df3fd48ea6e343cfb04b69aecc35066d3a23ddd9b2',
//                 'password' => '201196247a2b39b1ae27f36e6e96f4018405ea20dc19521a995693f9b9a5d143',
//                 'sender' => 'c09dacfe3011fd3051603426339dad970ad19b3865bf42f438ae749174695c04',
//                 'mobile' => $mobile,
//                 'template' => '57e397e922136ebb17480888a534825d58e19dbeea6aadfd633a8aa4ef0629ed',
//                 'Reply' => (string)$msg,

//             ]
//         ];
//     // $url = 'https://smsmisr.com/api/webapi/';
//     $url = 'https://smsmisr.com/api/OTP/';
//     $response = $client->request('POST', $url, $data);
//     return handleResponse($response);

// }

// function send_sms($mobile, $msg)
// {
//     $mobile = '20' . $mobile;
//     $client = new Client();
//     $data = [
//         'headers' => [
//             'Accept' => 'application/json',
//             'Content-Type' => 'application/json',
//         ],
//         'query' => [
//             'environment' => 1,
//             'username' => 'b7dc3bd52a4d92613a6bb9df3fd48ea6e343cfb04b69aecc35066d3a23ddd9b2',
//             'password' => '201196247a2b39b1ae27f36e6e96f4018405ea20dc19521a995693f9b9a5d143',
//             'sender' => 'c09dacfe3011fd3051603426339dad970ad19b3865bf42f438ae749174695c04',
//             'mobile' => $mobile,
//             'template' => '57e397e922136ebb17480888a534825d58e19dbeea6aadfd633a8aa4ef0629ed',
//             'otp' => (string)$msg,

//         ]
//     ];
//     // $url = 'https://smsmisr.com/api/webapi/';
//     $url = 'https://smsmisr.com/api/OTP/';
//     $response = $client->request('POST', $url, $data);
//     return handleResponse($response);
// }
function handleResponse($response)
{
    switch ($response->getStatusCode()) {
        case 404:
            return ['status' => 404, 'message' => "No Data Found!"];
            break;
        default:
            return json_decode($response->getBody()->getContents(), true);
            break;
    }
}
function online_users()
{
    $client = new Client([
        'verify' => false
    ]);
    $online_users = $client->request('GET', setting('url_echo') . '/apps/' . setting('echo_app_id') . '/channels/presence-online/users', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . setting('echo_auth_key')
        ],
    ]);
    return collect(json_decode($online_users->getBody()->getContents(), true)['users']);
}

function channel_users($channel_name)
{
    $client = new Client([
        'verify' => false
    ]);
    $online_users = $client->request('GET', setting('url_echo') . '/apps/' . setting('echo_app_id') . '/channels/' . $channel_name . '/users', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . setting('echo_auth_key')
        ],
    ]);
    return collect(json_decode($online_users->getBody()->getContents(), true)['users']);
}

function validateIfPhoneStartWithZero($phone)
{
    $first_number = substr($phone, 0, 1);
    if ($first_number == '0') {
        $phone = substr($phone, 1);
    }
    return $phone;
}

function root($category)
{
    if($category != null && property_exists($category, "parent")) {

        $parent = $category->parent;

        if ($parent and property_exists($parent, "position") and $parent->position == 'main') {
            return $parent;
        } elseif (!$parent) {
            return $category;
        }
        return root($parent);

    } else {
        return $category;
    }

}

function thirdLavels($category)
{
    // dd($category);
    $child = Category::where('parent_id',$category->id)->first();
    if ($child and $child->position == 'second_sub') {
        return $category->where('parent_id',$category->id)->get();
    } elseif ($child and $child->position == 'first_sub') {
        return Category::whereIn('parent_id', $category->children()->pluck('id')->toArray())->where('position', 'second_sub')->get();
    }

    return null;
}

function lastLevel($category, $child_doesnt_have_children = null)
{
    $child_doesnt_have_children ? $child_doesnt_have_children->push($category->children()->whereDoesntHave('children')->get()) : $child_doesnt_have_children = $category->children()->whereDoesntHave('children')->get();
    $child_have_children = $category->children()->whereHas('children')->get();

    if (count($child_have_children) > 0) {
        foreach ($child_have_children as $child) {
            lastLevel($child, $child_doesnt_have_children);
        }
    }
    return $child_doesnt_have_children->flatten()->unique();
}

// function lastLevel($category)
// {
//     $child_doesnt_have_children = $category->children()->whereDoesntHave('children')->get();
//     $child_have_children = $category->children()->whereHas('children')->get();

//     $child_doesnt_have_children->push(itretable($child_have_children));

//     return $child_doesnt_have_children;
// }

// function itretable($child_have_children, $child_doesnt_have_children_array = [])
// {
//     $child_doesnt_have_children = \App\Models\Category::whereIn('parent_id', $child_have_children)->select('id')->whereDoesntHave('children')->get();
//     $child_have_children = \App\Models\Category::whereIn('parent_id', $child_have_children)->select('id')->whereHas('subcategories')->get();

//     array_merge($child_doesnt_have_children_array, $child_doesnt_have_children);

//     if (count($child_have_children) != 0)
//     {
//         itretable($child_have_children, $child_doesnt_have_children_array);
//     }

//     return $child_doesnt_have_children_array;
// }

// function lastLevelIds($category_ids, $child_doesnt_have_children = [])
// {
//     $child_doesnt_have_children = array_merge($child_doesnt_have_children, $category_ids);
//     foreach(array_chunk($category_ids, 5000) as $array)
//     {
//         $children = \App\Models\Category::whereIn('parent_id', $category_ids)->select('id')->whereDoesntHave('subcategories')->pluck('id')->toArray();
//         $child_doesnt_have_children = array_merge($child_doesnt_have_children, $children);
//         $sub_categories = \App\Models\Category::whereIn('parent_id', $category_ids)->select('id')->whereHas('subcategories')->pluck('id')->toArray();
//         if(count($sub_categories) > 0)
//         {
//             $child_doesnt_have_children = lastLevelIds($sub_categories, $child_doesnt_have_children);
//         }
//     }
//     return $child_doesnt_have_children;
// }

function allThirdLavels($category)
{
    $child = $category->children()->first();

    if ($child and $child->position == 'second_sub') {
        return $category->children;
    } elseif ($child and $child->position == 'first_sub') {
        return Category::whereIn('parent_id', $category->children()->pluck('id')->toArray())->where('position', 'second_sub')->get();
    }

    return null;
}

function favGuestAndUser($request)
{
    $guestFavProducts = FavouriteProduct::where(['guest_token' => $request['guest_token'], 'user_id' => null])->get();
    if ($guestFavProducts) {
        foreach ($guestFavProducts as $guestProduct) {
            $userFav = FavouriteProduct::where(['product_detail_id' => $guestProduct->product_detail_id, 'user_id' => auth('api')->id(), 'guest_token' => null])->first();
            if ($userFav) {
                $guestProduct->delete();
            } else {
                $guestProduct->update([
                    'guest_token' => null,
                    'user_id' => auth('api')->id(),
                ]);
            }
        }
    }
    return true;
}

function rate_avg($id)
{
    $avg = round(OrderRate::where('product_detail_id', $id)->avg('rate'), 2);
    return $avg;
}

function generate_random_file_name($length = 12)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $generate_random_image_key = '';
    for ($i = 0; $i < $length; $i++) {
        $generate_random_image_key .= $characters[rand(0, $charactersLength - 1)];
    }
    return $generate_random_image_key;
}
function upload_single_file($request_file, $path)
{

    $name = time() . '_' . generate_random_file_name() . '.' . $request_file->getClientOriginalExtension();
    $request_file->move(storage_path($path), $name);

    return $name;
}

function pushFcm($fcmData, $userIds, $model = '\\App\\Models\\Device')
{
    $send_process = [];
    $fail_process = [];
    if (is_array($userIds) && !empty($userIds)) {
        $number_of_drivers = null;
        if ($model == '\\App\\Models\\Driver') {
            $model = '\\App\\Models\\Device';
            // $number_of_drivers = 1;
        }
        $devices = $model::whereIn('user_id', $userIds)/*->distinct('device_token')*/->latest()->when($number_of_drivers, function ($q) use ($number_of_drivers) {
            $q->take($number_of_drivers);
        })->get();
        $ios_devices = array_filter($devices->where('type', 'ios')->pluck('device_token')->toArray());
        $android_devices = array_filter($devices->where('type', 'android')->pluck('device_token')->toArray());

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder($fcmData['title']);
        $notificationBuilder->setBody($fcmData['body'])
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($fcmData);

        $option       = $optionBuilder->build();
        $data         = $dataBuilder->build();
        if (count($ios_devices)) {
            $notification = $notificationBuilder->build();
            // You must change it to get your tokens
            $downstreamResponse = FCM::sendTo($ios_devices, $option, $notification, $data);
            Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
            // return $downstreamResponse;
            $send_process[] = $downstreamResponse->numberSuccess();
        }
        if (count($android_devices)) {
            $notification = null;
            // You must change it to get your tokens
            $downstreamResponse = FCM::sendTo($android_devices, $option, $notification, $data);
            Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
            // return $downstreamResponse;
            // dd($downstreamResponse->numberSuccess());
            $send_process[] = $downstreamResponse->numberSuccess();
            // code...
        }
        return count($send_process);
    }
    // dd('ooooooooo');
    return "No Users";
}

/**
 * Push Notifications to phone FCM
 *
 * @param  array $fcmData
 * @param  array $userIds
 */
function pushFluterFcmNotes($fcmData, $userIds)
{
    if (isset($fcmData['title']) && is_array($fcmData['title']) && isset($fcmData['body']) && is_array($fcmData['body'])) {
        $fcmData['title'] = $fcmData['title'][app()->getLocale()];
        $fcmData['body'] = $fcmData['body'][app()->getLocale()];
    }

    $send_process = [];
    $fail_process = [];

    if (is_array($userIds) && !empty($userIds)) {
        $devices = Device::whereIn('user_id', $userIds)->pluck('device_token')->toArray();
        if (count($devices)) {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);
            $notificationBuilder = new PayloadNotificationBuilder($fcmData['title']);
            $notificationBuilder->setBody($fcmData['body'])->setSound('default');
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData($fcmData);
            $option       = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data         = $dataBuilder->build();
            // You must change it to get your tokens
            $downstreamResponse = FCM::sendTo($devices, $option, $notification, $data);
            Device::whereIn('device_token', $downstreamResponse->tokensToDelete() + array_keys($downstreamResponse->tokensWithError()))->delete();
            // return $downstreamResponse;
            return $downstreamResponse->numberSuccess();
        }
        return 0;
    }
    return "No Users";
}

function getCategorySizes($category, $sizes = null)
{
    $sizes ? $sizes->push($category->sizes) : $sizes = $category->sizes;
    info( $sizes);
    info($category->parent);
    if ($category->parent) {
        info('khlood');
        getCategorySizes($category->parent, $sizes);
    }
    return $sizes->flatten()->unique();
}


function getFinalCategory($categoryId, $array = [])
{
    $category = Category::find($categoryId);

    if ($category->children()->count() == 0) {
        $array += [$category->id];
    }
    if ($category->children()->count() > 0) {
        $children = $category->children;
        foreach ($children  as $child) {
            // dd(  'gadgad',$category->children);
            getFinalCategory($child->id, $array);
        }
    }
    return $array;
}
function getCategoryFeatures($category, $features = null)
{
    $features ? $features->push($category->features) : $features = $category->features;

    if ($category->parent) {
        getCategoryFeatures($category->parent, $features);
    }

    return $features->flatten()->unique();
}

function RemoveZeroQty()
{
    CartProduct::where('quantity',0)->delete();
}


function point2point_distance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
{
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else if ($unit == 'M') {
        return ($miles * $miles * 1609.344);
    } else {
        return round($miles, 2);
    }
}


function orderStatus($statusInArray,  $currentStatus, $orderStatusTimes)
{
    if ($statusInArray == $currentStatus) {
        return 'in_progress';
    } else {
        return strpos($orderStatusTimes, $statusInArray) ? 'done' : 'waiting';
    }
}

// pending , admin_accept , admin_rejected, admin_cancel , client_cancel  , admin_shipping , admin_delivered , client_finished
function OrderStatusIcon($status)
{
    switch ($status) {
        case 'pending':
            return asset('dashboardAssets/order_icons/pending.png');
            break;
        case 'admin_accept':
            return asset('dashboardAssets/order_icons/shipping.png');
            break;
        case 'admin_rejected':
            return   asset('dashboardAssets/order_icons/cancel.png');
            break;
        case 'admin_cancel':
            return   asset('dashboardAssets/order_icons/cancel.png');
            break;
        case 'client_cancel':
            return   asset('dashboardAssets/order_icons/cancel.png');
            break;
        case 'admin_shipping':
            return    asset('dashboardAssets/order_icons/shipping.png');
            break;
        case 'admin_delivered':
            return    asset('dashboardAssets/order_icons/completed.png');
            break;
        default:
    }
}
