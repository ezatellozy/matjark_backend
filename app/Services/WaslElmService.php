<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Carbon\Carbon;

trait WaslElmService
{
    private static $API_URL = "https://wasl.api.elm.sa/api/dispatching/v2/";
    private $client;
    private $headers;
    private $additionalParams;
    public $configs;

    public function registerDriver($driver)
    {
        $body_data = [
            'driver' => [
                'identityNumber' =>(string) $driver->identity_number,
                'dateOfBirthHijri' => (string)$driver->date_of_birth_hijri->format("Y/m/d"),
                'dateOfBirthGregorian' => (string)$driver->date_of_birth->format("Y-m-d"),
                'emailAddress' => (string)$driver->email,
                'mobileNumber' => (string)"+".$driver->phone
            ],
            'vehicle' => [
                'sequenceNumber' => (string)$driver->car->license_serial_number,
                'plateLetterRight' => (string)$driver->car->plate_letter_right,
                'plateLetterMiddle' => (string)$driver->car->plate_letter_middle,
                'plateLetterLeft' => (string)$driver->car->plate_letter_left,
                'plateNumber' => (string)$driver->car->plate_numbers_only,
                'plateType' => (string)$driver->car->plate_type,
            ]
        ];
        $url = self::$API_URL . "drivers";
        return $this->sendClientRequest('POST',$url,$body_data);

    }

    public function driverVehicleEligibility($driver)
    {
        $url = self::$API_URL . "drivers/eligibility/" . $driver->identity_number;
        return $this->sendClientRequest('GET',$url);
    }

    public function updateDriverLocation($driver,$license_serial_number = null)
    {
        $body_data = [
            'locations' => [
                [
                    'driverIdentityNumber' => $driver->identity_number,
                    'vehicleSequenceNumber' => $license_serial_number ?? $driver->car->license_serial_number,
                    'latitude' => (float)$driver->driver->lat,
                    'longitude' => (float)$driver->driver->lng,
                    'hasCustomer' => (boolean)!$driver->driver->is_available,
                    'updatedWhen' => date('Y-m-d\TH:i:s.u'),
               ]
            ]
        ];
        $url = self::$API_URL . "locations";
        return $this->sendClientRequest('POST',$url,$body_data);
    }

    public function finishTrip($order)
    {
        $driver = $order->driver;
        $car = $order->car;
        $start_location = $order->start_location_data;
        $arrive_location = $order->arrive_location_data;
        $on_way = date("Y-m-d H:i:s",strtotime(optional($order->order_status_times)->shipped));
        $start_at = date("Y-m-d H:i:s",strtotime(optional($order->order_status_times)->start_trip));
        $diff_in_seconds = Carbon::parse($start_at)->diffInSeconds($on_way);
        $body_data = [
            "sequenceNumber" => (string)$car->license_serial_number,
            "driverId" => (string)$driver->identity_number,
            "tripId" => (int)$order->id,
            "distanceInMeters" => (int) ($order->distance ? number_format($order->distance,2) : 1),
            "durationInSeconds" => (int)number_format(($order->actual_time ?? $order->expected_time) ,2),
            "customerRating" => (float)$order->rates()->where(['driver_id' => $order->driver_id])->avg('rates.rate'),
            "customerWaitingTimeInSeconds" => (int)$diff_in_seconds,
            "originCityNameInArabic" => '',
            "destinationCityNameInArabic" => '',
            "originLatitude" => (float)$start_location['lat'],
            "originLongitude" => (float)$start_location['lng'],
            "destinationLatitude" => (float)$arrive_location['lat'],
            "destinationLongitude" => (float)$arrive_location['lng'],
            "pickupTimestamp" => date('Y-m-d\TH:i:s.u', strtotime($start_at)),
            "dropoffTimestamp" => date('Y-m-d\TH:i:s.u', strtotime($order->finished_at)),
            "startedWhen" => date('Y-m-d\TH:i:s.u', strtotime($order->created_at)),
            "tripCost" => (float)$order->total_price
        ];

        $url = self::$API_URL . "trips";
        return $this->sendClientRequest('POST',$url,$body_data);
    }


    public function test_driver_register()
    {
        $body_data = [
            'driver' => [
                "identityNumber" => '1024545843462',
                "dateOfBirthHijri" => date('Y/m/d', strtotime('16-11-1403')),
                "dateOfBirthGregorian" => date('Y-m-d', strtotime('24-08-1983')),
                "emailAddress" => 'support@amnuh.com',
                "mobileNumber" => '+966558665731'
            ],
            'vehicle' => [
                "sequenceNumber" => '524241610',
                "plateLetterRight" => 'D',
                "plateLetterMiddle" => 'J',
                "plateLetterLeft" => 'Z',
                "plateNumber" => '1013',
                "plateType" => "1"
            ]
        ];
        $url = self::$API_URL . 'drivers';
        return $this->sendClientRequest('POST',$url,$body_data);
    }
    private function setConfigs()
    {
        $this->configs = \Config::get('wasl');
        $this->client = new Client();
        $this->headers = ['headers' => [
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Accept-Language' => app()->getLocale() == 'ar' ? 'ar-sa' : 'en-US',
           'app-id' => $this->configs['app_id'],
           'app-key' => $this->configs['app_key'],
           'client-id' => $this->configs['client_id'],
           ]];
        $this->additionalParams = [];
    }

    public function sendClientRequest($http_method,$url,$body = null)
    {
        try {
            $this->setConfigs();
            $data = $this->headers;
            if ($body) {
                $data = $this->headers+['body' => json_encode($body)];
            }
            $response = $this->client->request($http_method,$url, $data);
            return self::handleResponse($response);
        } catch (\Exception $e) {
            return self::handleResponse($e->getResponse());
        }
    }


    public static function handleResponse($response)
    {
        switch($response->getStatusCode()){
           case 404 :
              return ['status' => 404 , 'message' => "No Data Found!"];
              break;
           default:
              return json_decode($response->getBody()->getContents(),true);
              break;
        }
    }
}
