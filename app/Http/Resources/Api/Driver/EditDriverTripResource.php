<?php

namespace App\Http\Resources\Api\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class EditDriverTripResource extends JsonResource
{
    /**
     * make new request to edit driver trip info .
     *
     * @param   $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'driver_id' => $request->driver_id,
            'route_id' => $request->route_id,
            'schedule_id' => $request->schedule_id,
            'day_off' => $request->day_off,
        ];
    }
}
