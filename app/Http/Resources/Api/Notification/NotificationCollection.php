<?php

namespace App\Http\Resources\Api\Notification;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\{Order , Chat , OrderOffer};

class NotificationCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'unreadnotifications_count' => auth()->guard('api')->user()->unreadnotifications->count(),
            'notifications' => NotificationResource::collection($this->collection)
        ];
    }


}
